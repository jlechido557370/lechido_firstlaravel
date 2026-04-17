<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AuthorFollow;
use App\Models\Book;
use App\Models\Follow;
use App\Models\UserBook;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserBookController extends Controller
{
    const MAX_PER_DAY = 2;

    public function create()
    {
        return view('user.publish');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Check daily limit
        $todayCount = UserBook::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= self::MAX_PER_DAY) {
            return back()->with('error', 'You can only submit ' . self::MAX_PER_DAY . ' books per day. Try again tomorrow.');
        }

        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'author'         => ['required', 'string', 'max:255'],
            'isbn'           => ['nullable', 'string', 'max:50'],
            'genre'          => ['required', 'string', 'max:100'],
            'published_year' => ['nullable', 'integer', 'min:1', 'max:' . date('Y')],
            'description'    => ['nullable', 'string', 'max:5000'],
            'read_url'       => ['nullable', 'url', 'max:500'],
            'cover_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:3072'],
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('book-covers', 'public');
        }

        UserBook::create([
            'user_id'        => $user->id,
            'title'          => $data['title'],
            'author'         => $data['author'],
            'isbn'           => $data['isbn'] ?? null,
            'genre'          => $data['genre'],
            'published_year' => $data['published_year'] ?? null,
            'description'    => $data['description'] ?? null,
            'read_url'       => $data['read_url'] ?? null,
            'cover_image'    => $coverPath,
            'status'         => 'pending',
        ]);

        ActivityLog::record('book_submitted', "{$user->displayName()} submitted a book for review: {$data['title']}");

        return back()->with('success', 'Book submitted for review. It will be published once approved by staff.');
    }

    public function approve(UserBook $userBook)
    {
        if (!auth()->user()->isAdminOrStaff()) abort(403);

        // Copy to main books table
        $book = Book::create([
            'title'          => $userBook->title,
            'author'         => $userBook->author,
            'isbn'           => $userBook->isbn ?? 'UB-' . $userBook->id . '-' . time(),
            'genre'          => $userBook->genre,
            'published_year' => $userBook->published_year ?? date('Y'),
            'total_copies'   => 1,
            'available_copies' => 1,
            'description'    => $userBook->description,
            'read_url'       => $userBook->read_url,
            'cover_image'    => $userBook->cover_image,
        ]);

        $userBook->update([
            'status'       => 'approved',
            'reviewed_by'  => auth()->id(),
            'reviewed_at'  => now(),
        ]);

        // Notify submitter
        UserNotification::create([
            'user_id' => $userBook->user_id,
            'type'    => 'book_approved',
            'message' => "Your book \"{$userBook->title}\" has been approved and published.",
        ]);

        // Notify followers of the submitter and followers of the author
        $submitter = $userBook->user;
        if ($submitter) {
            $followerIds = Follow::where('following_id', $submitter->id)
                ->pluck('follower_id');

            foreach ($followerIds as $followerId) {
                if ($followerId !== $userBook->user_id) {
                    UserNotification::create([
                        'user_id' => $followerId,
                        'type'    => 'followed_user_published',
                        'message' => $submitter->displayName() . " published a new book: \"{$userBook->title}\".",
                    ]);
                }
            }
        }

        // Notify users following this author
        $authorFollowerIds = AuthorFollow::where('author_name', $userBook->author)
            ->pluck('user_id');

        foreach ($authorFollowerIds as $uid) {
            if ($uid !== $userBook->user_id) {
                UserNotification::create([
                    'user_id' => $uid,
                    'type'    => 'followed_author_published',
                    'message' => "Author \"{$userBook->author}\" has a new book: \"{$userBook->title}\".",
                ]);
            }
        }

        ActivityLog::record('book_submission_approved', auth()->user()->displayName() . " approved book submission: {$userBook->title}");

        return back()->with('success', "Book \"{$userBook->title}\" approved and published.");
    }

    public function reject(Request $request, UserBook $userBook)
    {
        if (!auth()->user()->isAdminOrStaff()) abort(403);

        $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);

        $userBook->update([
            'status'           => 'rejected',
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        UserNotification::create([
            'user_id' => $userBook->user_id,
            'type'    => 'book_rejected',
            'message' => "Your book \"{$userBook->title}\" was not approved. Reason: {$request->rejection_reason}",
        ]);

        ActivityLog::record('book_submission_rejected', auth()->user()->displayName() . " rejected book submission: {$userBook->title}");

        return back()->with('success', "Book \"{$userBook->title}\" rejected.");
    }

    public function mySubmissions()
    {
        $user        = auth()->user();
        $submissions = UserBook::where('user_id', $user->id)->latest()->get();
        return view('user.my_submissions', compact('submissions'));
    }
}