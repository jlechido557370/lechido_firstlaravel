<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowRecord;
use Illuminate\Support\Facades\Http;

class BookReadController extends Controller
{
    public function read(Book $book)
    {
        // Must have an active borrow
        $hasBorrowed = BorrowRecord::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if (! $hasBorrowed) {
            return redirect()->route('books.show', $book->id)
                ->with('error', 'You must borrow this book before you can read it.');
        }

        // Use cached Google Books ID, or look it up now
        $googleBooksId  = $book->google_books_id;
        $webReaderLink  = null;
        $previewLink    = null;
        $googleFound    = false;

        if (! $googleBooksId) {
            $googleBooksId = $this->lookupGoogleBooksId($book);

            // Cache it on the book so we don't hit the API again
            if ($googleBooksId) {
                $book->update(['google_books_id' => $googleBooksId]);
            }
        }

        if ($googleBooksId) {
            $googleFound   = true;
            $webReaderLink = "https://books.google.com/books?id={$googleBooksId}&lpg=PP1&pg=PP1&output=embed";
            $previewLink   = "https://books.google.com/books?id={$googleBooksId}";
        }

        return view('books.read', compact(
            'book',
            'googleBooksId',
            'googleFound',
            'webReaderLink',
            'previewLink'
        ));
    }

    /**
     * Search Google Books API by ISBN first, then title+author as fallback.
     * No API key required for basic searches (free quota: 1000 requests/day).
     * Add GOOGLE_BOOKS_API_KEY to .env and config/services.php for higher limits.
     */
    private function lookupGoogleBooksId(Book $book): ?string
    {
        $apiKey = config('services.google_books.key');
        $params = ['maxResults' => 1];
        if ($apiKey) {
            $params['key'] = $apiKey;
        }

        // 1. Try ISBN search first (most accurate)
        $isbnClean = preg_replace('/[^0-9X]/', '', strtoupper($book->isbn));
        if ($isbnClean) {
            $params['q'] = 'isbn:' . $isbnClean;
            $result = $this->fetchGoogleBooksVolume($params);
            if ($result) return $result;
        }

        // 2. Fallback: title + author
        $params['q'] = 'intitle:' . urlencode($book->title) . '+inauthor:' . urlencode($book->author);
        return $this->fetchGoogleBooksVolume($params);
    }

    private function fetchGoogleBooksVolume(array $params): ?string
    {
        try {
            $response = Http::timeout(5)
                ->get('https://www.googleapis.com/books/v1/volumes', $params);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['items'][0]['id'])) {
                    return $data['items'][0]['id'];
                }
            }
        } catch (\Exception $e) {
            // Silently fail — don't break the page if Google is unreachable
        }

        return null;
    }
}