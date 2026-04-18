@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="card">
        <h1>My Profile</h1>
        <p class="muted">Member since {{ $user->created_at->format('F d, Y') }}</p>
        <p><a href="{{ route('user.public_profile', $user->id) }}">View public profile</a> &nbsp;&bull;&nbsp; <a href="{{ route('user.ratings') }}">View my ratings</a></p>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <h2>Profile Picture</h2>
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                <img src="{{ $user->avatarUrl() }}" alt="avatar" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb;">
                <div>
                    <div style="font-weight: bold; font-size: 16px;">{{ $user->badgedName() }}</div>
                    <div class="muted" style="font-size: 13px;">{{ ucfirst($user->role) }}</div>
                    <div class="muted" style="font-size: 13px;">Joined {{ $user->created_at->format('M Y') }}</div>
                    @if($user->isSubscribed())
                        <div style="font-size: 13px; color: #92400e; margin-top: 4px;">Subscriber — expires {{ $user->subscription_expires_at?->format('M d, Y') }}</div>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('user.avatar.update') }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 12px;">
                    <label>Upload New Picture (JPG, PNG, GIF, WEBP — max 2MB)</label>
                    <input type="file" name="avatar" accept="image/*" required>
                    @error('avatar')<div style="color:#b91c1c; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <button type="submit" style="margin-bottom: 8px;">Upload Picture</button>
            </form>

            @if($user->avatar)
                <form method="POST" action="{{ route('user.avatar.remove') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: #dc2626; border-color: #dc2626;">Remove Picture</button>
                </form>
            @endif
        </div>

        <div class="card">
            <h2>Update Profile</h2>
            <form method="POST" action="{{ route('user.profile.update') }}">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 12px;">
                    <label>Username <span class="muted" style="font-size:12px;">(letters, numbers, underscores)</span></label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required pattern="[a-zA-Z0-9_]+" minlength="3" maxlength="30">
                    @error('username')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom: 12px;">
                    <label>Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom: 12px;">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom: 12px;">
                    <label>Bio <span class="muted" style="font-size:12px;">(max 500 characters)</span></label>
                    <textarea name="bio" rows="4" maxlength="500">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom: 12px;">
                    <label>Gender</label>
                    <div style="display:flex; gap:20px; margin-top:6px; flex-wrap:wrap;">
                        @foreach(['male' => 'Male', 'female' => 'Female', 'prefer_not_to_say' => 'Prefer not to say'] as $val => $label)
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-weight:normal;">
                                <input type="radio" name="gender" value="{{ $val }}" {{ old('gender', $user->gender) === $val ? 'checked' : '' }} style="width:auto;">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div style="margin-bottom: 12px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:normal;">
                        <input type="checkbox" name="allow_dms" value="1" {{ $user->allow_dms ? 'checked' : '' }} style="width:auto;">
                        Allow other users to send me direct messages
                    </label>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:normal;">
                        <input type="checkbox" name="hide_real_name" value="1" {{ $user->hide_real_name ? 'checked' : '' }} style="width:auto;">
                        Hide my real name from the public (only your username will be shown)
                    </label>
                </div>
                <button type="submit">Save Profile</button>
            </form>
        </div>
    </div>

    <div class="card">
        <h2>Change Password</h2>
        <form method="POST" action="{{ route('user.password.update') }}" style="max-width: 400px;">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 12px;">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
                @error('current_password')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
            </div>
            <div style="margin-bottom: 12px;">
                <label>New Password</label>
                <input type="password" name="password" required>
                @error('password')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
            </div>
            <div style="margin-bottom: 12px;">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <button type="submit">Change Password</button>
        </form>
    </div>

    <div class="card">
        <h2>Public Profile</h2>
        <p class="muted">Others can view your public profile at the link below.</p>
        <a href="{{ route('user.public_profile', $user->id) }}" target="_blank">View my public profile &nearr;</a>
    </div>

    <div class="card">
        <h2>My Ratings</h2>
        @if($ratings->isEmpty())
            <p class="muted">You have not rated any books yet.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ratings as $rating)
                        <tr>
                            <td><a href="{{ route('books.show', ['book' => $rating->book_id, 'back' => request()->fullUrl()]) }}">{{ $rating->book->title ?? 'Deleted Book' }}</a></td>
                            <td>{{ $rating->rating }}/5</td>
                            <td>{{ $rating->comment ?: '—' }}</td>
                            <td>{{ $rating->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card">
        <h2>Borrow History</h2>
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Borrowed</th>
                    <th>Returned</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($borrowHistory as $record)
                    <tr>
                        <td>{{ $record->book->title ?? 'Deleted Book' }}</td>
                        <td>{{ $record->borrowed_at?->format('M d, Y') }}</td>
                        <td>{{ $record->returned_at?->format('M d, Y') ?? '—' }}</td>
                        <td>
                            @if($record->returned_at)
                                <span class="badge badge-green">Returned</span>
                            @else
                                <span class="badge">Active</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">No borrow history yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection