<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\UserBook;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $section = request('section', 'overview');

        $stats = [
            'total_books'    => Book::count(),
            'active_borrows' => BorrowRecord::whereNull('returned_at')->count(),
            'overdue'        => BorrowRecord::whereNull('returned_at')->where('due_date', '<', now())->count(),
            'total_users'    => User::where('role', 'user')->count(),
            'pending_fines'  => BorrowRecord::where('fine_amount', '>', 0)->where('fine_paid', false)->sum('fine_amount'),
            'pending_books'  => UserBook::where('status', 'pending')->count(),
        ];

        $borrowings   = BorrowRecord::with(['book', 'user'])->latest()->get();
        $logs         = ActivityLog::with('user')->latest()->take(200)->get();
        $reservations = Reservation::with(['book', 'user'])->latest()->get();
        $submissions  = UserBook::with('user')->latest()->get();

        // Users with optional ID search
        $usersQuery = User::where('role', 'user')->with('borrowRecords')->latest();
        if ($userSearch = request('user_search')) {
            $usersQuery->where(function ($q) use ($userSearch) {
                $q->where('id', $userSearch)
                  ->orWhere('name', 'like', "%{$userSearch}%")
                  ->orWhere('username', 'like', "%{$userSearch}%")
                  ->orWhere('email', 'like', "%{$userSearch}%");
            });
        }
        $users = $usersQuery->get();

        // Payments with optional receipt ID search
        $paymentsQuery = Payment::with(['user', 'borrowRecord.book'])->latest();
        if ($receiptSearch = request('receipt_search')) {
            $paymentsQuery->where(function ($q) use ($receiptSearch) {
                $q->where('id', $receiptSearch)
                  ->orWhere('finverse_payment_id', 'like', "%{$receiptSearch}%")
                  ->orWhere('finverse_link_id', 'like', "%{$receiptSearch}%");
            });
        }
        $payments = $paymentsQuery->get();

        return view('staff.dashboard', compact(
            'section', 'stats', 'borrowings',
            'users', 'logs', 'reservations', 'payments', 'submissions'
        ));
    }

    public function returnBook(BorrowRecord $borrowing)
    {
        if ($borrowing->returned_at) {
            return back()->with('error', 'Already returned.');
        }

        $fine = $borrowing->calculateFine();
        $borrowing->update(['returned_at' => now(), 'fine_amount' => $fine]);

        if ($borrowing->book) {
            $borrowing->book->increment('available_copies');
        }

        $fineTxt = $fine > 0 ? " Fine: ₱{$fine}" : '';
        ActivityLog::record('book_returned', "Staff processed return.{$fineTxt}", ['borrow_id' => $borrowing->id]);

        return back()->with('success', "Book returned.{$fineTxt}");
    }

    public function fulfillReservation(Reservation $reservation)
    {
        $reservation->update(['status' => 'fulfilled']);
        ActivityLog::record('reservation_fulfilled', "Staff fulfilled reservation.");
        return back()->with('success', 'Reservation fulfilled.');
    }

    public function cancelReservation(Reservation $reservation)
    {
        $reservation->update(['status' => 'cancelled']);
        ActivityLog::record('reservation_cancelled', "Staff cancelled reservation.");
        return back()->with('success', 'Reservation cancelled.');
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        UserNotification::create([
            'user_id' => $request->user_id,
            'type'    => 'info',
            'message' => $request->message,
        ]);

        ActivityLog::record('notification_sent', "Staff sent notification to user ID {$request->user_id}.");

        return back()->with('success', 'Notification sent.');
    }
}