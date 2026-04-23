<?php
namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    const FINVERSE_API   = 'https://api.prod.finverse.net';
    const MONTHLY_PRICE  = 99;
    const YEARLY_PRICE   = 999;

    const PAYMENT_METHODS = [
        'gcash'          => 'GCash',
        'maya'           => 'Maya',
        'bank_transfer'  => 'Bank Transfer',
        'cash'           => 'Cash at Counter',
    ];

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
            Log::error('Finverse subscription token error', ['body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Finverse subscription token exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function index()
    {
        $user = auth()->user();
        return view('subscription.index', compact('user'));
    }

    public function confirmPage(Request $request)
    {
        $request->validate(['plan' => ['required', 'in:monthly,yearly']]);
        $plan           = $request->plan;
        $amount         = $plan === 'yearly' ? self::YEARLY_PRICE : self::MONTHLY_PRICE;
        $label          = $plan === 'yearly' ? 'Yearly (12 months)' : 'Monthly (1 month)';
        $user           = auth()->user();
        $paymentMethods = self::PAYMENT_METHODS;
        return view('subscription.confirm', compact('plan', 'amount', 'label', 'user', 'paymentMethods'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan'           => ['required', 'in:monthly,yearly'],
            'payment_method' => ['required', 'in:gcash,maya,bank_transfer,cash'],
        ]);

        $user          = auth()->user();
        $amount        = $request->plan === 'yearly' ? self::YEARLY_PRICE : self::MONTHLY_PRICE;
        $months        = $request->plan === 'yearly' ? 12 : 1;
        $label         = ucfirst($request->plan);
        $paymentMethod = $request->payment_method;

        $paymentUrl  = null;
        $accessToken = $this->getAccessToken();

        if ($accessToken && in_array($paymentMethod, ['gcash', 'maya'])) {
            try {
                $response = Http::withToken($accessToken)->timeout(15)
                    ->post(self::FINVERSE_API . '/payment-links', [
                        'amount'       => (int) round($amount * 100),
                        'currency'     => 'PHP',
                        'description'  => "Library {$label} Subscription",
                        'redirect_url' => route('subscription.callback', ['plan' => $request->plan]),
                        'webhook_url'  => route('subscription.webhook'),
                        'metadata'     => [
                            'user_id'        => $user->id,
                            'plan'           => $request->plan,
                            'payment_method' => $paymentMethod,
                        ],
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $paymentUrl = $data['payment_url'] ?? $data['url'] ?? null;
                    session([
                        'sub_link_id'        => $data['link_id'] ?? $data['id'] ?? null,
                        'sub_plan'           => $request->plan,
                        'sub_payment_method' => $paymentMethod,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Finverse subscription link exception', ['error' => $e->getMessage()]);
            }
        }

        if ($paymentUrl) {
            return redirect()->away($paymentUrl);
        }

        // Fallback: cash, bank_transfer, or Finverse not configured
        $payment = $this->activateSubscription($user, $months, $amount, $request->plan, $paymentMethod);

        return redirect()->route('payments.receipt', $payment->id)
            ->with('success', "Subscription activated for {$label} plan.");
    }

    public function callback(Request $request)
    {
        $user   = auth()->user();
        $status = $request->get('status');
        $plan   = $request->get('plan', session('sub_plan', 'monthly'));
        $method = session('sub_payment_method', 'online');
        $months = $plan === 'yearly' ? 12 : 1;
        $amount = $plan === 'yearly' ? self::YEARLY_PRICE : self::MONTHLY_PRICE;

        if ($status === 'success' || $status === 'completed') {
            $payment = $this->activateSubscription($user, $months, $amount, $plan, $method);
            return redirect()->route('payments.receipt', $payment->id)
                ->with('success', 'Subscription activated successfully.');
        }

        return redirect()->route('subscription.index')
            ->with('error', 'Payment was not completed. Please try again.');
    }

    public function webhook(Request $request)
    {
        $data   = $request->all();
        $status = $data['status'] ?? null;
        $userId = $data['metadata']['user_id'] ?? null;
        $plan   = $data['metadata']['plan'] ?? 'monthly';
        $method = $data['metadata']['payment_method'] ?? 'online';

        Log::info('Subscription webhook received', $data);

        if (($status === 'success' || $status === 'completed') && $userId) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $months = $plan === 'yearly' ? 12 : 1;
                $amount = $plan === 'yearly' ? self::YEARLY_PRICE : self::MONTHLY_PRICE;
                $this->activateSubscription($user, $months, $amount, $plan, $method);
            }
        }

        return response()->json(['received' => true]);
    }

    /**
     * Legacy receipt route (kept for direct URL access).
     * Used when no payment ID is available (e.g., old links).
     */
    public function receipt(Request $request)
    {
        $user        = auth()->user();
        $plan        = $request->get('plan', 'monthly');
        $amount      = $request->get('amount', $plan === 'yearly' ? self::YEARLY_PRICE : self::MONTHLY_PRICE);
        $method      = $request->get('method', 'N/A');
        $label       = $plan === 'yearly' ? 'Yearly (12 months)' : 'Monthly (1 month)';
        $methodLabel = self::PAYMENT_METHODS[$method] ?? ucfirst($method);
        $expiresAt   = $user->subscription_expires_at;
        $payment     = null;

        return view('subscription.receipt', compact('user', 'plan', 'label', 'amount', 'method', 'methodLabel', 'expiresAt', 'payment'));
    }

    public function cancel(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSubscribed()) {
            return back()->with('error', 'You do not have an active subscription.');
        }

  $user->update([
            'is_subscribed'           => false,
            'subscription_expires_at' => null,
            'subscription_plan'       => null,
        ]);

        // Downgrade role back to user on cancellation
        if ($user->role === 'subscribed_user') {
            $user->update(['role' => 'user']);
        }

        UserNotification::create([
            'user_id' => $user->id,
            'type'    => 'subscription_cancelled',
            'message' => 'Your subscription has been cancelled. Your account has returned to the free plan.',
        ]);

        ActivityLog::record('subscription_cancelled', $user->displayName() . ' cancelled their subscription.');

        return redirect()->route('subscription.index')
            ->with('success', 'Your subscription has been cancelled.');
    }

    /**
     * Activate subscription, create Payment record, fire notifications.
     * Returns the created Payment so callers can redirect to its receipt.
     */
    private function activateSubscription(\App\Models\User $user, int $months, float $amount, string $plan, string $paymentMethod): Payment
    {
        $expiresAt = $user->subscription_expires_at && $user->subscription_expires_at->isFuture()
            ? $user->subscription_expires_at->addMonths($months)
            : now()->addMonths($months);

 $user->update([
            'is_subscribed'           => true,
            'subscription_expires_at' => $expiresAt,
            'subscription_plan'       => $plan,
            'subscription_amount'     => $amount,
            'subscription_paid_at'    => now(),
        ]);

        // Auto-upgrade role to subscribed_user on payment
        if ($user->role === 'user') {
            $user->update(['role' => 'subscribed_user']);
        }

        // Store payment record for payment history & receipt access
        $payment = Payment::create([
            'user_id'                 => $user->id,
            'type'                    => 'subscription',
            'amount'                  => $amount,
            'status'                  => 'completed',
            'paid_at'                 => now(),
            'payment_method'          => $paymentMethod,
            'subscription_plan'       => $plan,
            'subscription_expires_at' => $expiresAt,
        ]);

        UserNotification::create([
            'user_id' => $user->id,
            'type'    => 'subscription_activated',
            'message' => "Your subscription is now active until {$expiresAt->format('M d, Y')}. Borrow up to 25 books and publish up to 50!",
        ]);

        ActivityLog::record('subscription_activated', $user->displayName() . " activated subscription until {$expiresAt->format('M d, Y')}.");

        return $payment;
    }
}