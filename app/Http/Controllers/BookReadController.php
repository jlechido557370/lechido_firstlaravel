<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowRecord;

class BookReadController extends Controller
{
    public function read(Book $book)
    {
        // Check user has an active borrow for this book
        $hasBorrowed = BorrowRecord::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if (! $hasBorrowed) {
            return redirect()->route('books.show', $book->id)
                ->with('error', 'You must borrow this book before you can read it.');
        }

        if (! $book->read_url) {
            return redirect()->route('books.show', $book->id)
                ->with('error', 'No online version is available for this book.');
        }

        return view('books.read', compact('book'));
    }
}