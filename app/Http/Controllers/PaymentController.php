<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BorrowRecord;
use App\Models\Payment;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    const FINVERSE_API = 'https://api.prod.finverse.net';

    private function getAccessToken(): ?string
    {
        try {
            $response = Http::timeout(15)->post(self::FINVERSE_API . '/auth/customer/token', [
                'client_id'     => config('services.finverse.client_id'),
                'client_secret' => config('services.finverse.client_secret'),
                'grant_type'    => 'client_credentials',
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('Finverse token error', ['body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Finverse token exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    const PAYMENT_METHODS = [
        'gcash'         => 'GCash',
        'maya'          => 'Maya',
        'bank_transfer' => 'Bank Transfer',
        'cash'          => 'Cash at Counter',
    ];

    public function initiate(BorrowRecord $borrowing)
    {
        $user = auth()->user();

        if ($borrowing->user_id !== $user->id) {
            return back()->with('error', 'Unauthorized.');
        }

        $fine = $borrowing->calculateFine();

        if ($fine <= 0) {
            return back()->with('error', 'No fine to pay for this borrow record.');
        }

        if ($borrowing->fine_paid) {
            return back()->with('error', 'This fine has already been paid.');
        }

        $borrowing->payments()->where('status', 'pending')->update(['status' => 'cancelled']);

        $accessToken = $this->getAccessToken();
        $paymentUrl  = null;
        $linkId      = null;
        $finverseResponse = null;

        if ($accessToken) {
            try {
                $response = Http::withToken($accessToken)
                    ->timeout(15)
                    ->post(self::FINVERSE_API . '/payment-links', [
                        'amount'       => (int) round($fine * 100),
                        'currency'     => 'PHP',
                        'description'  => 'Library fine for: ' . ($borrowing->book->title ?? 'Book'),
                        'redirect_url' => route('payments.callback'),
                        'webhook_url'  => route('payments.webhook'),
                        'metadata'     => [
                            'borrow_record_id' => $borrowing->id,
                            'user_id'          => $user->id,
                        ],
                    ]);

                if ($response->successful()) {
                    $data       = $response->json();
                    $paymentUrl = $data['payment_url'] ?? $data['url'] ?? null;
                    $linkId     = $data['link_id'] ?? $data['id'] ?? null;
                    $finverseResponse = $data;
                } else {
                    Log::error('Finverse payment link error', ['body' => $response->body()]);
                }
            } catch (\Exception $e) {
                Log::error('Finverse payment link exception', ['error' => $e->getMessage()]);
            }
        }

        $payment = Payment::create([
            'user_id'           => $user->id,
            'borrow_record_id'  => $borrowing->id,
            'amount'            => $fine,
            'status'            => 'pending',
            'finverse_link_id'  => $linkId,
            'payment_url'       => $paymentUrl,
            'finverse_response' => $finverseResponse,
        ]);

        $bookTitle = $borrowing->book->title ?? 'book';
        ActivityLog::record(
            'payment_initiated',
            "{$user->name} initiated payment of ₱{$fine} for '{$bookTitle}'.",
            ['payment_id' => $payment->id, 'borrow_record_id' => $borrowing->id]
        );

        if ($paymentUrl) {
            return redirect()->away($paymentUrl);
        }

        $paymentMethods = self::PAYMENT_METHODS;
        return view('payments.confirm', compact('payment', 'borrowing', 'fine', 'paymentMethods'));
    }

    /**
     * Handle the payment method form submission from the confirm page.
     */
    public function process(Request $request, BorrowRecord $borrowing)
    {
        $request->validate([
            'payment_method' => ['required', 'in:gcash,maya,bank_transfer,cash'],
        ]);

        $user = auth()->user();

        if ($borrowing->user_id !== $user->id) {
            return back()->with('error', 'Unauthorized.');
        }

        $fine = $borrowing->calculateFine();

        if ($fine <= 0 || $borrowing->fine_paid) {
            return back()->with('error', 'No fine to pay or already paid.');
        }

        $payment = $borrowing->payments()->where('status', 'pending')->latest()->first();

        if (!$payment) {
            $payment = Payment::create([
                'user_id'          => $user->id,
                'borrow_record_id' => $borrowing->id,
                'amount'           => $fine,
                'status'           => 'pending',
            ]);
        }

        $payment->update(['payment_method' => $request->payment_method]);

        // For cash/bank_transfer, mark complete immediately and show receipt
        if (in_array($request->payment_method, ['cash', 'bank_transfer'])) {
            $this->markPaymentComplete($payment, ['payment_method' => $request->payment_method]);
            return redirect()->route('payments.receipt', $payment->id);
        }

        // For online methods (gcash, maya) — attempt Finverse or fall back to receipt
        $accessToken = $this->getAccessToken();
        if ($accessToken) {
            try {
                $response = Http::withToken($accessToken)->timeout(15)
                    ->post(self::FINVERSE_API . '/payment-links', [
                        'amount'       => (int) round($fine * 100),
                        'currency'     => 'PHP',
                        'description'  => 'Library fine for: ' . ($borrowing->book->title ?? 'Book'),
                        'redirect_url' => route('payments.callback'),
                        'webhook_url'  => route('payments.webhook'),
                        'metadata'     => ['borrow_record_id' => $borrowing->id, 'user_id' => $user->id],
                    ]);
                if ($response->successful()) {
                    $data = $response->json();
                    $url  = $data['payment_url'] ?? $data['url'] ?? null;
                    if ($url) {
                        $payment->update(['finverse_link_id' => $data['link_id'] ?? $data['id'] ?? null, 'payment_url' => $url]);
                        return redirect()->away($url);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Finverse process exception', ['error' => $e->getMessage()]);
            }
        }

        // Finverse not available — treat same as cash
        $this->markPaymentComplete($payment, ['payment_method' => $request->payment_method]);
        return redirect()->route('payments.receipt', $payment->id);
    }

    /**
     * Show the payment receipt page (with popup modal).
     */
    public function receipt(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized.');
        }

        $borrowing   = $payment->borrowRecord;
        $methodLabel = self::PAYMENT_METHODS[$payment->payment_method ?? ''] ?? ucfirst($payment->payment_method ?? 'N/A');

        return view('payments.payments_receipt', compact('payment', 'borrowing', 'methodLabel'));
    }

    public function callback(Request $request)
    {
        $status = $request->get('status');
        $linkId = $request->get('link_id') ?? $request->get('payment_link_id');

        if ($linkId) {
            $payment = Payment::where('finverse_link_id', $linkId)->first();
            if ($payment) {
                if ($status === 'success' || $status === 'completed') {
                    $this->markPaymentComplete($payment, ['callback_data' => $request->all()]);
                    return redirect()->route('payments.receipt', $payment->id);
                }

                $payment->update([
                    'status'         => 'failed',
                    'failure_reason' => $request->get('error') ?? $status,
                ]);
                return redirect()->route('user.dashboard')
                    ->with('error', 'Payment was not completed. Please try again.');
            }
        }

        return redirect()->route('user.dashboard')
            ->with('error', 'Could not verify payment. Contact support if amount was deducted.');
    }

    public function webhook(Request $request)
    {
        $data   = $request->all();
        $linkId = $data['link_id'] ?? $data['payment_link_id'] ?? null;
        $status = $data['status'] ?? null;

        Log::info('Finverse webhook received', $data);

        if (!$linkId) {
            return response()->json(['error' => 'Missing link_id'], 400);
        }

        $payment = Payment::where('finverse_link_id', $linkId)->first();
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($status === 'success' || $status === 'completed') {
            $this->markPaymentComplete($payment, $data);
        } elseif ($status === 'failed') {
            $payment->update([
                'status'            => 'failed',
                'failure_reason'    => $data['error'] ?? 'Payment failed',
                'finverse_response' => $data,
            ]);
        }

        return response()->json(['received' => true]);
    }

    public function manualConfirm(Request $request, Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized.');
        }

        if (!$payment->isPending()) {
            return back()->with('error', 'This payment is no longer pending.');
        }

        $this->markPaymentComplete($payment, ['manual' => true]);

        return redirect()->route('payments.receipt', $payment->id);
    }

    private function markPaymentComplete(Payment $payment, array $extra = []): void
    {
        $payment->update([
            'status'            => 'completed',
            'paid_at'           => now(),
            'finverse_response' => array_merge($payment->finverse_response ?? [], $extra),
        ]);

        $borrowing = $payment->borrowRecord;
        if ($borrowing) {
            $borrowing->update(['fine_paid' => true]);
        }

        UserNotification::create([
            'user_id'          => $payment->user_id,
            'type'             => 'payment_confirmed',
            'message'          => 'Your payment of ₱' . number_format($payment->amount, 2) . ' has been confirmed.',
            'borrow_record_id' => $payment->borrow_record_id,
        ]);

        ActivityLog::record(
            'payment_completed',
            "Payment of ₱{$payment->amount} confirmed for user ID {$payment->user_id}.",
            ['payment_id' => $payment->id]
        );
    }
}