<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookReview;
use App\Models\BorrowRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $borrowHistory = BorrowRecord::with('book')
            ->where('user_id', $user->id)
            ->latest()->get();

        $ratings = BookReview::with('book')
            ->where('user_id', $user->id)
            ->latest()->take(10)->get();

        return view('user.profile', compact('user', 'borrowHistory', 'ratings'));
    }

    public function publicProfile(User $user)
    {
        $borrowCount = BorrowRecord::where('user_id', $user->id)->count();
        $ratings     = BookReview::with('book')
            ->where('user_id', $user->id)
            ->latest()->take(6)->get();

        $isFollowing = auth()->check() ? auth()->user()->isFollowing($user) : false;

        return view('user.public_profile', compact('user', 'borrowCount', 'ratings', 'isFollowing'));
    }

    public function ratings()
    {
        $user    = auth()->user();
        $ratings = BookReview::with(['book', 'book.reviews'])
            ->where('user_id', $user->id)
            ->latest()->get();

        return view('user.ratings', compact('user', 'ratings'));
    }

    public function publicRatings(User $user)
    {
        $ratings = BookReview::with(['book', 'book.reviews'])
            ->where('user_id', $user->id)
            ->latest()->get();

        return view('user.ratings', compact('user', 'ratings'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'username'       => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('users')->ignore($user->id)],
            'email'          => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'bio'            => ['nullable', 'string', 'max:500'],
            'gender'         => ['nullable', Rule::in(['male', 'female', 'prefer_not_to_say'])],
            'allow_dms'      => ['boolean'],
            'hide_real_name' => ['boolean'],
        ]);

        $data['allow_dms']      = $request->has('allow_dms');
        $data['hide_real_name'] = $request->has('hide_real_name');

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Profile picture updated.');
    }

    public function removeAvatar()
    {
        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Profile picture removed.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }
}