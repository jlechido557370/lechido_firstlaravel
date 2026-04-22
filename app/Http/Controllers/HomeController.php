<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Bookmark;
use App\Models\BorrowRecord;
use App\Models\Reservation;
use App\Models\User;
use App\Models\BookReview;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private function applyFilters($query)
    {
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%")
                  ->orWhere('published_year', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('isbn_13', 'like', "%{$search}%")
                  ->orWhere('isbn_10', 'like', "%{$search}%");
            });
        }

        if ($genre = request('genre')) {
            $query->where('genre', $genre);
        }

        if (($type = request('type')) === 'book') {
            $query->where('book_type', 'book');
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
            'year_asc'   => $query->orderBy('published_year', 'asc'),
            'year_desc'  => $query->orderBy('published_year', 'desc'),
            'title_asc'  => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            default      => $query->latest(),
        };

        return $query;
    }

    public function index()
    {
        $stats = [
            'total_books'     => Book::where('book_type', 'book')->count(),
            'available_books' => Book::where('book_type', 'book')->sum('available_copies'),
            'active_borrows'  => BorrowRecord::whereNull('returned_at')->count(),
            'members'         => User::where('role', 'user')->count(),
        ];

        $booksQuery = $this->applyFilters(Book::where('book_type', 'book'));
        $books = $booksQuery->get();
        $genres = Book::select('genre')
            ->distinct()
            ->whereNotNull('genre')
            ->whereNotIn('genre', ['Manga', 'Comic'])
            ->where('book_type', 'book')
            ->orderBy('genre')
            ->pluck('genre');

        return view('home', compact('stats', 'books', 'genres'));
    }

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
            $user   = auth()->user();

            $currentBorrowings = BorrowRecord::where('user_id', $userId)
                ->whereNull('returned_at')
                ->pluck('book_id');

            $reservations = Reservation::where('user_id', $userId)
                ->where('status', 'pending')
                ->pluck('book_id');

            $limit           = $user->isSubscribed() ? 25 : 5;
            $alreadyBorrowed = $currentBorrowings->contains($book->id);
            $alreadyReserved = $reservations->contains($book->id);
            $atLimit         = $currentBorrowings->count() >= $limit;
            $isBookmarked    = Bookmark::where('user_id', $userId)->where('book_id', $book->id)->exists();
            $canRead         = $alreadyBorrowed;

            $userReview = BookReview::where('user_id', $userId)->where('book_id', $book->id)->first();
        }

        $borrowCount = BorrowRecord::where('book_id', $book->id)->count();
        $editHistory = $book->editHistories()->with('user')->take(30)->get();
        $related     = Book::where('genre', $book->genre)
                           ->where('id', '!=', $book->id)
                           ->latest()
                           ->take(4)
                           ->get();

        $reviews   = $book->reviews()->with('user')->get();
        $avgRating = $book->average_rating;

        return view('books.show', compact(
            'book', 'alreadyBorrowed', 'alreadyReserved', 'atLimit',
            'isBookmarked', 'borrowCount', 'editHistory', 'related',
            'canRead', 'userReview', 'reviews', 'avgRating'
        ));
    }

    public function catalogue()
    {
        $excludedGenres = ['Manga', 'Comic'];

        $genres = Book::select('genre')
            ->distinct()
            ->whereNotNull('genre')
            ->whereNotIn('genre', $excludedGenres)
            ->where('book_type', 'book')
            ->orderBy('genre')
            ->pluck('genre');

        $byGenre = [];
        foreach ($genres as $genre) {
            $byGenre[$genre] = Book::where('genre', $genre)
                ->where('book_type', 'book')
                ->latest()
                ->get();
        }

        $totalBooks = Book::where('book_type', 'book')->count();

        return view('books.catalogue', compact('genres', 'byGenre', 'totalBooks'));
    }

    public function bookmarks()
    {
        $bookmarks = Bookmark::where('user_id', auth()->id())
            ->with('book')
            ->latest()
            ->get();

        return view('books.bookmarks', compact('bookmarks'));
    }

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

    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $books = collect();
        $users = collect();

        if ($q !== '') {
            $books = Book::query()
                ->where('book_type', 'book')
                ->whereNotIn('genre', ['Manga', 'Comic'])
                ->where(function ($query) use ($q) {
                    $query->where('title', 'like', "%{$q}%")
                        ->orWhere('author', 'like', "%{$q}%")
                        ->orWhere('genre', 'like', "%{$q}%")
                        ->orWhere('isbn', 'like', "%{$q}%")
                        ->orWhere('isbn_13', 'like', "%{$q}%")
                        ->orWhere('isbn_10', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                })
                ->latest()
                ->take(25)
                ->get();

            $users = User::query()
                ->where(function ($query) use ($q) {
                    $query->where('username', 'like', "%{$q}%")
                        ->orWhere('bio', 'like', "%{$q}%")
                        ->orWhere(function ($q2) use ($q) {
                            // Only match on real name if the user hasn't hidden it
                            $q2->where('hide_real_name', false)
                               ->where('name', 'like', "%{$q}%");
                        });
                })
                ->latest()
                ->take(25)
                ->get();
        }

        return view('search', compact('q', 'books', 'users'));
    }
}