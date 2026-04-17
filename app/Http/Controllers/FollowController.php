<?php

namespace App\Http\Controllers;

use App\Models\AuthorFollow;
use App\Models\Follow;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function toggleUser(User $user)
    {
        $me = auth()->user();

        if ($me->id === $user->id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        $exists = Follow::where('follower_id', $me->id)
            ->where('following_id', $user->id)->first();

        if ($exists) {
            $exists->delete();
            return back()->with('success', 'Unfollowed ' . $user->displayName() . '.');
        }

        Follow::create([
            'follower_id'  => $me->id,
            'following_id' => $user->id,
        ]);

        // Notify the followed user
        UserNotification::create([
            'user_id' => $user->id,
            'type'    => 'new_follower',
            'message' => $me->displayName() . ' started following you.',
        ]);

        return back()->with('success', 'Now following ' . $user->displayName() . '.');
    }

    public function toggleAuthor(Request $request)
    {
        $request->validate(['author_name' => ['required', 'string', 'max:255']]);

        $me     = auth()->user();
        $author = $request->author_name;

        $exists = AuthorFollow::where('user_id', $me->id)
            ->where('author_name', $author)->first();

        if ($exists) {
            $exists->delete();
            return back()->with('success', 'Unfollowed author: ' . $author . '.');
        }

        AuthorFollow::create(['user_id' => $me->id, 'author_name' => $author]);
        return back()->with('success', 'Now following author: ' . $author . '.');
    }

    public function following()
    {
        $user = auth()->user();

        $followingUsers   = $user->following()->get();
        $followingAuthors = $user->authorFollows()->get();

        return view('user.following', compact('followingUsers', 'followingAuthors'));
    }
}