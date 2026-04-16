<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Bookmark;
use App\Models\BookEditHistory;
use App\Models\BorrowRecord;
use App\Models\Reservation;
use App\Models\User;
use App\Models\BookReview; // make sure this is added

class HomeController extends Controller
{
    // ── Shared filter logic ──────────────────────────────────────────────────
    private function applyFilters($query)
    {
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

        if ($availability = request('availability')) {
            if ($availability === 'available') {
                $query->where('available_copies', '>', 0);
            } elseif ($availability === 'unavailable') {
                $query->where('available_copies', 0);
            }
        }

        $sort = request('sort', 'latest');
        match ($sort) {
            'year_asc'    => $query->orderBy('published_year', 'asc'),
            'year_desc'   => $query->orderBy('published_year', 'desc'),
            'title_asc'   => $query->orderBy('title', 'asc'),
            'title_desc'  => $query->orderBy('title', 'desc'),
            default       => $query->latest(),
        };

        return $query;
    }

    // ── Home page ────────────────────────────────────────────────────────────
    public function index()
    {
        $stats = [
            'total_books'    => Book::count(),
            'available_books'=> Book::sum('available_copies'),
            'active_borrows' => BorrowRecord::whereNull('returned_at')->count(),
            'members'        => User::where('role', 'user')->count(),
        ];

        $books  = $this->applyFilters(Book::query())->get();
        $genres = Book::select('genre')->distinct()->orderBy('genre')->pluck('genre');

        return view('home', compact('stats', 'books', 'genres'));
    }

    // ── Book detail page ─────────────────────────────────────────────────────
    public function show(Book $book)
    {
        $alreadyBorrowed = false;
        $alreadyReserved = false;
        $atLimit         = false;
        $isBookmarked    = false;
        $canRead         = false;
        $userReview      = null;

        if (auth()->check()) {
            $userId = auth()->id();

            $currentBorrowings = BorrowRecord::where('user_id', $userId)
                ->whereNull('returned_at')
                ->pluck('book_id');

            $reservations = Reservation::where('user_id', $userId)
                ->where('status', 'pending')
                ->pluck('book_id');

            $alreadyBorrowed = $currentBorrowings->contains($book->id);
            $alreadyReserved = $reservations->contains($book->id);
            $atLimit         = $currentBorrowings->count() >= 5;
            $isBookmarked    = Bookmark::where('user_id', $userId)
                                       ->where('book_id', $book->id)
                                       ->exists();

            // User can read if they have an active borrow AND book has a read_url
            $canRead = $alreadyBorrowed && !empty($book->read_url);

            // Load this user's existing review
            $userReview = BookReview::where('user_id', $userId)
                                    ->where('book_id', $book->id)
                                    ->first();
        }

        $borrowCount = BorrowRecord::where('book_id', $book->id)->count();
        $editHistory = $book->editHistories()->with('user')->take(30)->get();
        $related     = Book::where('genre', $book->genre)
                           ->where('id', '!=', $book->id)
                           ->latest()
                           ->take(4)
                           ->get();

        // All reviews for this book
        $reviews   = $book->reviews()->with('user')->get();
        $avgRating = $book->average_rating;

        return view('books.show', compact(
            'book', 'alreadyBorrowed', 'alreadyReserved', 'atLimit',
            'isBookmarked', 'borrowCount', 'editHistory', 'related',
            'canRead', 'userReview', 'reviews', 'avgRating'
        ));
    }

    // ── Catalogue page (browse by genre) ─────────────────────────────────────
    public function catalogue()
    {
        $genres = Book::select('genre')->distinct()->orderBy('genre')->pluck('genre');

        $byGenre = [];
        foreach ($genres as $genre) {
            $byGenre[$genre] = Book::where('genre', $genre)->latest()->get();
        }

        $totalBooks = Book::count();

        return view('books.catalogue', compact('genres', 'byGenre', 'totalBooks'));
    }

    // ── Bookmarks page ───────────────────────────────────────────────────────
    public function bookmarks()
    {
        $bookmarks = Bookmark::where('user_id', auth()->id())
            ->with('book')
            ->latest()
            ->get();

        return view('books.bookmarks', compact('bookmarks'));
    }

    // ── Toggle bookmark ──────────────────────────────────────────────────────
    public function toggleBookmark(Book $book)
    {
        $userId   = auth()->id();
        $existing = Bookmark::where('user_id', $userId)->where('book_id', $book->id)->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', "Removed \"{$book->title}\" from bookmarks.");
        }

        Bookmark::create(['user_id' => $userId, 'book_id' => $book->id]);
        return back()->with('success', "Bookmarked \"{$book->title}\".");
    }
}