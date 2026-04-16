<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\BookEditHistory;
use App\Models\BorrowRecord;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $section     = request('section', 'overview');
        $editingBook = request('edit') ? Book::find(request('edit')) : null;

        $stats = [
            'total_books'      => Book::count(),
            'available_copies' => Book::sum('available_copies'),
            'active_borrows'   => BorrowRecord::whereNull('returned_at')->count(),
            'total_users'      => User::count(),
        ];

        $books        = Book::latest()->get();
        $borrowings   = BorrowRecord::with(['book', 'user'])->latest()->get();
        $users        = User::latest()->get();
        $logs         = ActivityLog::with('user')->latest()->take(200)->get();
        $reservations = Reservation::with(['book', 'user'])->latest()->get();

        return view('admin.dashboard', compact(
            'section', 'stats', 'books', 'borrowings',
            'users', 'editingBook', 'logs', 'reservations'
        ));
    }

    public function storeBook(Request $request)
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'author'         => ['required', 'string', 'max:255'],
            'isbn'           => ['required', 'string', 'max:50', 'unique:books,isbn'],
            'genre'          => ['required', 'string', 'max:100'],
            'published_year' => ['required', 'integer', 'min:1', 'max:' . date('Y')],
            'total_copies'   => ['required', 'integer', 'min:1'],
            'description'    => ['nullable', 'string'],
            'read_url'       => ['nullable', 'url', 'max:500'],
        ]);

        $data['available_copies'] = $data['total_copies'];
        $book = Book::create($data);

        // Record creation in edit history
        BookEditHistory::create([
            'book_id'       => $book->id,
            'user_id'       => auth()->id(),
            'field_changed' => 'book',
            'old_value'     => null,
            'new_value'     => $book->title,
            'action'        => 'created',
        ]);

        ActivityLog::record('book_added', "Admin added book: {$book->title}", ['book_id' => $book->id]);

        return redirect()
            ->route('admin.dashboard', ['section' => 'books'])
            ->with('success', 'Book added successfully.');
    }

    public function updateBook(Request $request, Book $book)
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'author'         => ['required', 'string', 'max:255'],
            'isbn'           => ['required', 'string', 'max:50', 'unique:books,isbn,' . $book->id],
            'genre'          => ['required', 'string', 'max:100'],
            'published_year' => ['required', 'integer', 'min:1', 'max:' . date('Y')],
            'total_copies'   => ['required', 'integer', 'min:1'],
            'description'    => ['nullable', 'string'],
            'read_url'       => ['nullable', 'url', 'max:500'],
        ]);

        // Track which fields changed
        $trackFields = ['title', 'author', 'isbn', 'genre', 'published_year', 'total_copies', 'description', 'read_url'];
        foreach ($trackFields as $field) {
            $oldVal = (string) ($book->$field ?? '');
            $newVal = (string) ($data[$field] ?? '');
            if ($oldVal !== $newVal) {
                BookEditHistory::create([
                    'book_id'       => $book->id,
                    'user_id'       => auth()->id(),
                    'field_changed' => $field,
                    'old_value'     => $oldVal,
                    'new_value'     => $newVal,
                    'action'        => 'updated',
                ]);
            }
        }

        $borrowedCount           = max($book->total_copies - $book->available_copies, 0);
        $data['available_copies'] = max($data['total_copies'] - $borrowedCount, 0);
        $book->update($data);

        ActivityLog::record('book_updated', "Admin updated book: {$book->title}", ['book_id' => $book->id]);

        return redirect()
            ->route('admin.dashboard', ['section' => 'books'])
            ->with('success', 'Book updated successfully.');
    }

    public function destroyBook(Book $book)
    {
        $title = $book->title;
        $book->delete();

        ActivityLog::record('book_deleted', "Admin deleted book: {$title}");

        return redirect()
            ->route('admin.dashboard', ['section' => 'books'])
            ->with('success', 'Book deleted successfully.');
    }

    public function returnBook(BorrowRecord $borrowing)
    {
        if ($borrowing->returned_at) {
            return back()->with('error', 'This book has already been returned.');
        }

        $fine = $borrowing->calculateFine();
        $borrowing->update(['returned_at' => now(), 'fine_amount' => $fine]);

        if ($borrowing->book) {
            $borrowing->book->increment('available_copies');

            $nextReservation = Reservation::where('book_id', $borrowing->book_id)
                ->where('status', 'pending')
                ->oldest()
                ->first();

            if ($nextReservation) {
                $notifTitle = $borrowing->book->title ?? 'book';
                ActivityLog::record(
                    'reservation_notified',
                    "Book '{$notifTitle}' is now available for reserved user.",
                    ['reservation_id' => $nextReservation->id]
                );
            }
        }

        $fineTxt   = $fine > 0 ? " Fine: ₱{$fine}" : '';
        $bookTitle = $borrowing->book->title ?? 'book';
        $userName  = $borrowing->user?->name ?? 'user';

        ActivityLog::record(
            'book_returned',
            "Admin processed return of '{$bookTitle}' by {$userName}.{$fineTxt}",
            ['borrow_id' => $borrowing->id, 'fine' => $fine]
        );

        return back()->with('success', "Book returned successfully.{$fineTxt}");
    }

    public function fulfillReservation(Reservation $reservation)
    {
        $bookTitle = $reservation->book->title ?? 'book';
        $userName  = $reservation->user?->name ?? 'user';

        $reservation->update(['status' => 'fulfilled']);
        ActivityLog::record('reservation_fulfilled', "Reservation fulfilled for '{$bookTitle}' by {$userName}.");
        return back()->with('success', 'Reservation marked as fulfilled.');
    }

    public function cancelReservation(Reservation $reservation)
    {
        $bookTitle = $reservation->book->title ?? 'book';
        $userName  = $reservation->user?->name ?? 'user';

        $reservation->update(['status' => 'cancelled']);
        ActivityLog::record('reservation_cancelled', "Reservation cancelled for '{$bookTitle}' by {$userName}.");
        return back()->with('success', 'Reservation cancelled.');
    }
}