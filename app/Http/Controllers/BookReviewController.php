<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReview;
use Illuminate\Http\Request;

class BookReviewController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        BookReview::updateOrCreate(
            ['user_id' => auth()->id(), 'book_id' => $book->id],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        return back()->with('success', 'Your review has been saved.');
    }

    public function destroy(Book $book)
    {
        BookReview::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->delete();

        return back()->with('success', 'Your review has been removed.');
    }
}