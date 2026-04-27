<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    const MAX_BORROWS_FREE = 5;
    const MAX_BORROWS_SUB  = 25;
    const LOAN_DAYS        = 10;

    /** Check if user can read without borrowing */
    private function canReadWithoutBorrowing(): bool
    {
        $user = auth()->user();
        return $user->isAdmin() || $user->isStaff();
    }

    public function index()
    {
        $user = auth()->user();

        $currentBorrowings = BorrowRecord::with('book')
            ->where('user_id', $user->id)
            ->whereNull('returned_at')
            ->get();

        $borrowingHistory = BorrowRecord::with(['book', 'payments'])
            ->where('user_id', $user->id)
            ->whereNotNull('returned_at')
            ->latest()
            ->get();

        $reservations = Reservation::with('book')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->get();

        $paymentLogs = Payment::with('borrowRecord.book')
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'borrowed_now' => $currentBorrowings->count(),
            'books_seen'   => BorrowRecord::where('user_id', $user->id)->count(),
            'returned'     => $borrowingHistory->count(),
            'overdue'      => $currentBorrowings->filter(fn($b) => $b->is_overdue)->count(),
        ];

        $totalFines = BorrowRecord::where('user_id', $user->id)
            ->where('fine_amount', '>', 0)
            ->where('fine_paid', false)
            ->sum('fine_amount');

        $this->generateNotifications($user->id, $currentBorrowings);

        return view('user.dashboard', compact(
            'stats', 'currentBorrowings', 'borrowingHistory',
            'reservations', 'totalFines', 'paymentLogs'
        ));
    }

    public function paymentHistory()
    {
        $user = auth()->user();

        $payments = Payment::with('borrowRecord.book')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('user.payment_history', compact('payments'));
    }

    private function generateNotifications(int $userId, $borrowings): void
    {
        foreach ($borrowings as $borrowing) {
            if ($borrowing->is_overdue) {
                $exists = UserNotification::where('user_id', $userId)
                    ->where('borrow_record_id', $borrowing->id)
                    ->where('type', 'overdue')
                    ->exists();

                if (! $exists) {
                    $bookTitle = $borrowing->book->title ?? 'a book';
                    UserNotification::create([
                        'user_id'          => $userId,
                        'type'             => 'overdue',
                        'message'          => "Your borrow of \"{$bookTitle}\" is overdue. Fine: ₱" . number_format($borrowing->calculateFine(), 2) . ' (₱5/day).',
                        'borrow_record_id' => $borrowing->id,
                    ]);
                }
            } elseif ($borrowing->is_due_soon) {
                $exists = UserNotification::where('user_id', $userId)
                    ->where('borrow_record_id', $borrowing->id)
                    ->where('type', 'due_soon')
                    ->exists();

                if (! $exists) {
                    $bookTitle = $borrowing->book->title ?? 'a book';
                    UserNotification::create([
                        'user_id'          => $userId,
                        'type'             => 'due_soon',
                        'message'          => "Your borrow of \"{$bookTitle}\" is due in less than 24 hours. Return by: " . $borrowing->due_date->format('M d, Y H:i') . '.',
                        'borrow_record_id' => $borrowing->id,
                    ]);
                }
            }
        }
    }

    public function borrowBook(Book $book)
    {
        $user  = auth()->user();
        $limit = $user->isSubscribed() ? self::MAX_BORROWS_SUB : self::MAX_BORROWS_FREE;

        $activeBorrows = BorrowRecord::where('user_id', $user->id)->whereNull('returned_at')->count();

        Log::info('Borrow attempt', [
            'user_id'         => $user->id,
            'role'            => $user->role,
            'is_subscribed'   => $user->is_subscribed,
            'sub_expires_at'  => $user->subscription_expires_at?->toDateTimeString(),
            'isSubscribed()'  => $user->isSubscribed(),
            'active_borrows'  => $activeBorrows,
            'limit'           => $limit,
            'book_id'         => $book->id,
        ]);

        if ($activeBorrows >= $limit) {
            if (! $user->isSubscribed()) {
                return back()->with('subscription_prompt', true)
                             ->with('error', 'You have reached the free limit of ' . self::MAX_BORROWS_FREE . ' borrowed books. Subscribe to borrow up to 25 at once.');
            }

            return back()->with('error', 'You have reached the maximum limit of ' . self::MAX_BORROWS_SUB . ' borrowed books.');
        }

        try {
            return DB::transaction(function () use ($book, $user) {
                $freshBook = Book::lockForUpdate()->find($book->id);

                if ($freshBook->available_copies <= 0) {
                    return back()->with('error', 'No copies available. You can reserve this book instead.');
                }

                $alreadyBorrowed = BorrowRecord::where('user_id', $user->id)
                    ->where('book_id', $book->id)->whereNull('returned_at')->exists();
                if ($alreadyBorrowed) {
                    return back()->with('error', 'You have already borrowed this book.');
                }

                BorrowRecord::create([
                    'user_id'     => $user->id,
                    'book_id'     => $book->id,
                    'borrowed_at' => now(),
                    'due_date'    => now()->addDays(self::LOAN_DAYS),
                ]);

                $freshBook->decrement('available_copies');

                ActivityLog::record(
                    'book_borrowed',
                    "{$user->name} borrowed '{$book->title}'. Due: " . now()->addDays(self::LOAN_DAYS)->format('M d, Y'),
                    ['book_id' => $book->id]
                );

                return back()->with('success', "You borrowed '{$book->title}'. Due in " . self::LOAN_DAYS . " days.");
            });
        } catch (\Exception $e) {
            Log::error('Borrow failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to borrow book. Please try again.');
        }
    }

    public function returnBook(BorrowRecord $borrowing)
    {
        if ($borrowing->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($borrowing->returned_at) {
            return back()->with('error', 'This book has already been returned.');
        }

        $fine = $borrowing->calculateFine();

        if ($fine > 0 && ! $borrowing->fine_paid) {
            return back()->with('error', "You have an outstanding fine of ₱{$fine}. Please pay your fine before returning the book.");
        }

        $borrowing->update(['returned_at' => now(), 'fine_amount' => $fine]);

        if ($borrowing->book) {
            $borrowing->book->increment('available_copies');
        }

        $fineTxt   = $fine > 0 ? " Fine: ₱{$fine}" : '';
        $fineColor = $fine > 0 ? 'warning' : 'success';

        ActivityLog::record(
            'book_returned',
            "{$borrowing->user->name} returned '{$borrowing->book->title}'.{$fineTxt}",
            ['book_id' => $borrowing->book_id]
        );

        return back()->with($fineColor, "You returned '{$borrowing->book->title}'.{$fineTxt}");
    }

    public function reserveBook(Book $book)
    {
        $user = auth()->user();

        if (BorrowRecord::where('user_id', $user->id)->where('book_id', $book->id)->whereNull('returned_at')->exists()) {
            return back()->with('error', 'You already borrowed this book.');
        }
        if (Reservation::where('user_id', $user->id)->where('book_id', $book->id)->where('status', 'pending')->exists()) {
            return back()->with('error', 'You already reserved this book.');
        }

        Reservation::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status'  => 'pending',
        ]);

        ActivityLog::record('book_reserved', "{$user->name} reserved '{$book->title}'", ['book_id' => $book->id]);

        return back()->with('success', "You reserved '{$book->title}'. We will notify you when it is available.");
    }

    public function cancelReservation(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($reservation->status !== 'pending') {
            return back()->with('error', 'This reservation is no longer pending.');
        }

        $title = $reservation->book->title ?? 'book';
        $reservation->update(['status' => 'cancelled']);

        ActivityLog::record('reservation_cancelled', auth()->user()->name . " cancelled reservation for '{$title}'", ['book_id' => $reservation->book_id]);

        return back()->with('success', "Your reservation for '{$title}' has been cancelled.");
    }
}
