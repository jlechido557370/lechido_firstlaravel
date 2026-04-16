<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    const MAX_BORROWS = 5;
    const LOAN_DAYS   = 10;

    public function index()
    {
        $user = auth()->user();

        $currentBorrowings = BorrowRecord::with('book')
            ->where('user_id', $user->id)
            ->whereNull('returned_at')
            ->get();

        $query = Book::query();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%")
                  ->orWhere('published_year', 'like', "%{$search}%");
            });
        }

        if ($genre = request('genre')) {
            $query->where('genre', $genre);
        }

        $sort = request('sort', 'latest');
        match ($sort) {
            'year_asc'   => $query->orderBy('published_year', 'asc'),
            'year_desc'  => $query->orderBy('published_year', 'desc'),
            'title_asc'  => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            default      => $query->latest(),
        };

        $books  = $query->get();
        $genres = Book::select('genre')->distinct()->orderBy('genre')->pluck('genre');

        $borrowingHistory = BorrowRecord::with('book')
            ->where('user_id', $user->id)
            ->whereNotNull('returned_at')
            ->latest()
            ->get();

        $reservations = Reservation::with('book')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->get();

        $stats = [
            'borrowed_now' => $currentBorrowings->count(),
            'books_seen'   => BorrowRecord::where('user_id', $user->id)->count(),
            'returned'     => $borrowingHistory->count(),
            'overdue'      => $currentBorrowings->filter(fn($b) => $b->is_overdue)->count(),
        ];

        $totalFines = BorrowRecord::where('user_id', $user->id)
            ->where('fine_amount', '>', 0)
            ->sum('fine_amount');

        return view('user.dashboard', compact(
            'stats', 'books', 'genres', 'currentBorrowings',
            'borrowingHistory', 'reservations', 'totalFines'
        ));
    }

    public function borrowBook(Book $book)
    {
        $user = auth()->user();

        $activeBorrows = BorrowRecord::where('user_id', $user->id)
            ->whereNull('returned_at')
            ->count();

        if ($activeBorrows >= self::MAX_BORROWS) {
            return back()->with('error', 'You have reached the maximum limit of ' . self::MAX_BORROWS . ' borrowed books.');
        }

        $alreadyBorrowed = BorrowRecord::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if ($alreadyBorrowed) {
            return back()->with('error', 'You have already borrowed this book.');
        }

        if ($book->available_copies <= 0) {
            return back()->with('error', 'No copies available. You can reserve this book instead.');
        }

        BorrowRecord::create([
            'user_id'     => $user->id,
            'book_id'     => $book->id,
            'borrowed_at' => now(),
            'due_date'    => now()->addDays(self::LOAN_DAYS),
        ]);

        $book->decrement('available_copies');

        ActivityLog::record(
            'book_borrowed',
            "{$user->name} borrowed '{$book->title}'. Due: " . now()->addDays(self::LOAN_DAYS)->format('M d, Y'),
            ['book_id' => $book->id]
        );

        return back()->with('success', "You borrowed '{$book->title}'. Due in " . self::LOAN_DAYS . " days.");
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
        $borrowing->update(['returned_at' => now(), 'fine_amount' => $fine]);

        if ($borrowing->book) {
            $borrowing->book->increment('available_copies');
        }

        $fineTxt   = $fine > 0 ? " Fine: ₱{$fine}" : '';
        $bookTitle = $borrowing->book->title ?? 'book';
        $userName  = auth()->user()?->name ?? 'user';

        ActivityLog::record(
            'book_returned',
            "{$userName} returned '{$bookTitle}'.{$fineTxt}",
            ['borrow_id' => $borrowing->id, 'fine' => $fine]
        );

        return back()->with('success', "Book returned.{$fineTxt}");
    }

    public function reserveBook(Book $book)
    {
        $user = auth()->user();

        $alreadyReserved = Reservation::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyReserved) {
            return back()->with('error', 'You already have a pending reservation for this book.');
        }

        if ($book->available_copies > 0) {
            return back()->with('error', 'This book is available — you can borrow it directly.');
        }

        Reservation::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        ActivityLog::record('book_reserved', "{$user->name} reserved '{$book->title}'.", ['book_id' => $book->id]);

        return back()->with('success', "'{$book->title}' has been reserved. You'll be notified when it's available.");
    }

    public function cancelReservation(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized.');
        }

        $bookTitle = $reservation->book->title ?? 'book';
        $userName  = auth()->user()?->name ?? 'user';

        $reservation->update(['status' => 'cancelled']);

        ActivityLog::record('reservation_cancelled', "{$userName} cancelled reservation for '{$bookTitle}'.");

        return back()->with('success', 'Reservation cancelled.');
    }
}