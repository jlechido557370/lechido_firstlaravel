<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookReview;
use App\Models\BorrowRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // ── Own profile page ─────────────────────────────────────────────────────
    public function show()
    {
        $user = auth()->user();

        $borrowHistory = BorrowRecord::with('book')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $ratings = BookReview::with('book')
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('user.profile', compact('user', 'borrowHistory', 'ratings'));
    }

    // ── Public profile (anyone can view) ─────────────────────────────────────
    public function publicProfile(User $user)
    {
        $borrowCount = BorrowRecord::where('user_id', $user->id)->count();
        $ratings = BookReview::with('book')
            ->where('user_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        return view('user.public_profile', compact('user', 'borrowCount', 'ratings'));
    }

    public function ratings()
    {
        $user = auth()->user();

        $ratings = BookReview::with(['book', 'book.reviews'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('user.ratings', compact('user', 'ratings'));
    }

    public function publicRatings(User $user)
    {
        $ratings = BookReview::with(['book', 'book.reviews'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('user.ratings', compact('user', 'ratings'));
    }

    // ── Update name / email / bio ─────────────────────────────────────────────
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'bio'   => ['nullable', 'string', 'max:500'],
        ]);

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    // ── Upload avatar ─────────────────────────────────────────────────────────
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        $user = auth()->user();

        // Delete old avatar if it exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar in storage/app/public/avatars/
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return back()->with('success', 'Profile picture updated.');
    }

    // ── Remove avatar ─────────────────────────────────────────────────────────
    public function removeAvatar()
    {
        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Profile picture removed.');
    }

    // ── Change password ───────────────────────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}