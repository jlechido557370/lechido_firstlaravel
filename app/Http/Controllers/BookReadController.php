<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowRecord;
use Illuminate\Support\Facades\Http;

class BookReadController extends Controller
{
    public function read(Book $book)
    {
        $backUrl = request('back');

        $user = auth()->user();
        $isAdminOrStaff = $user->isAdmin() || $user->isStaff();

        $hasBorrowed = $isAdminOrStaff ? true : BorrowRecord::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if (! $hasBorrowed && ! $isAdminOrStaff) {
            return redirect()->route('books.show', ['book' => $book->id, 'back' => request('back')])
                ->with('error', 'You must borrow this book before you can read it.');
        }

        $googleBooksId  = $book->google_books_id;
        $webReaderLink  = null;
        $previewLink    = null;
        $googleFound    = false;
        $readUrl = null;
        $isPdf = false;

        if (! $googleBooksId) {
            $googleBooksId = $this->lookupGoogleBooksId($book);
            if ($googleBooksId) {
                $book->update(['google_books_id' => $googleBooksId]);
            }
        }

        if ($googleBooksId) {
            $googleFound   = true;
            $webReaderLink = "https://books.google.com/books?id={$googleBooksId}&lpg=PP1&pg=PP1&output=embed";
            $previewLink   = "https://books.google.com/books?id={$googleBooksId}";
        } elseif ($book->read_url) {
            $readUrl = $book->read_url;
            $isPdf = str_ends_with(strtolower($readUrl), '.pdf');
        }

        return view('books.read', compact(
            'book',
            'googleBooksId',
            'googleFound',
            'webReaderLink',
            'previewLink',
            'backUrl',
            'readUrl',
            'isPdf'
        ));
    }

    private function lookupGoogleBooksId(Book $book): ?string
    {
        $apiKey = config('services.google_books.key');
        $params = ['maxResults' => 1];
        if ($apiKey) {
            $params['key'] = $apiKey;
        }

        $isbnClean = preg_replace('/[^0-9X]/', '', strtoupper($book->isbn));
        if ($isbnClean) {
            $params['q'] = 'isbn:' . $isbnClean;
            $result = $this->fetchGoogleBooksVolume($params);
            if ($result) {
                return $result;
            }
        }

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
            // Silently fail
        }

        return null;
    }
}
