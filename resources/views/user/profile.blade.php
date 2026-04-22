@extends('layouts.app')

@section('title', 'My Profile — dotLibrary')

@section('content')

{{-- ── HERO HEADER ── --}}
<div class="card" style="background: linear-gradient(135deg, var(--black) 0%, color-mix(in srgb, var(--black) 85%, transparent) 100%); color: var(--white); padding: 32px 36px; border: none; margin-bottom: 16px; position: relative; overflow: hidden;">
    <div style="position:absolute; top:-60px; right:-60px; width:220px; height:220px; border-radius:50%; border: 1px solid rgba(255,255,255,.05); pointer-events:none;"></div>
    <div style="position:absolute; top:-20px; right:-20px; width:140px; height:140px; border-radius:50%; border: 1px solid rgba(255,255,255,.07); pointer-events:none;"></div>
    <div style="display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
        <div style="position:relative; flex-shrink:0;">
            <img src="{{ $user->avatarUrl() }}" alt="avatar"
                 style="width:88px; height:88px; border-radius:50%; object-fit:cover; border: 2px solid rgba(255,255,255,.25); box-shadow: 0 4px 20px rgba(0,0,0,.4);">
            @if($user->isSubscribed())
                <div style="position:absolute; bottom:0; right:0; width:22px; height:22px; background:var(--white); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; border:2px solid var(--black);">✦</div>
            @endif
        </div>
        <div>
            <h1 style="font-size:28px; font-weight:500; color:var(--white); margin-bottom:4px;">{{ $user->badgedName() }}</h1>
            <div style="font-size:13px; color:rgba(255,255,255,.55); margin-bottom:8px; font-family:var(--font-mono); letter-spacing:.04em; text-transform:uppercase;">{{ ucfirst($user->role) }} &nbsp;·&nbsp; Member since {{ $user->created_at->format('F Y') }}</div>
            @if($user->isSubscribed())
                <div style="display:inline-block; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); padding:3px 10px; font-size:11px; font-family:var(--font-mono); letter-spacing:.05em; color:rgba(255,255,255,.8);">SUBSCRIBER — expires {{ $user->subscription_expires_at?->format('M d, Y') }}</div>
            @endif
        </div>
        <div style="margin-left:auto; display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('user.public_profile', $user->id) }}" style="color:rgba(255,255,255,.7); font-size:13px; border:1px solid rgba(255,255,255,.2); padding:7px 14px; display:inline-block;">View Public Profile</a>
            <a href="{{ route('user.ratings') }}" style="color:rgba(255,255,255,.7); font-size:13px; border:1px solid rgba(255,255,255,.2); padding:7px 14px; display:inline-block;">My Ratings</a>
        </div>
    </div>
</div>

{{-- ── EDIT PANELS ── --}}
<div class="grid grid-2" style="margin-bottom:16px;">

    {{-- Picture panel --}}
    <div class="card" style="padding:28px;">
        <h2 style="margin-bottom:20px;">Profile Picture</h2>

        <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; padding-bottom:24px; border-bottom:1px solid var(--mid);">
            <img src="{{ $user->avatarUrl() }}" alt="avatar"
                 style="width:72px; height:72px; border-radius:50%; object-fit:cover; border:1px solid var(--border); flex-shrink:0;">
            <div>
                <div style="font-weight:500; font-size:15px; margin-bottom:2px;">{{ $user->badgedName() }}</div>
                <div style="font-size:12px; color:var(--muted); margin-bottom:4px;">{{ ucfirst($user->role) }} &nbsp;·&nbsp; Joined {{ $user->created_at->format('M Y') }}</div>
                @if($user->isSubscribed())
                    <div style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">Subscriber — expires {{ $user->subscription_expires_at?->format('M d, Y') }}</div>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('user.avatar.update') }}" enctype="multipart/form-data">
            @csrf
            <label style="margin-bottom:10px;">Upload New Picture</label>
            <div style="font-size:12px; color:var(--muted); margin-bottom:8px;">JPG, PNG, GIF, WEBP &mdash; max 2MB</div>
            <input type="file" name="avatar" accept="image/*" required style="margin-bottom:12px;">
            @error('avatar')<div style="color:#dc2626; font-size:13px; margin-bottom:8px;">{{ $message }}</div>@enderror
            <button type="submit" style="width:100%; margin-top:4px;">Upload Picture</button>
        </form>

        @if($user->avatar)
            <form method="POST" action="{{ route('user.avatar.remove') }}" style="margin-top:10px;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" style="width:100%;">Remove Picture</button>
            </form>
        @endif
    </div>

    {{-- Update profile panel --}}
    <div class="card" style="padding:28px;">
        <h2 style="margin-bottom:20px;">Update Profile</h2>
        <form method="POST" action="{{ route('user.profile.update') }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:16px;">
                <label>Username <span style="font-size:11px; color:var(--muted); font-family:var(--font-mono); font-weight:400;">(letters, numbers, underscores)</span></label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required pattern="[a-zA-Z0-9_]+" minlength="3" maxlength="30">
                @error('username')<div style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:16px;">
                <label>Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<div style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:16px;">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')<div style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:20px;">
                <label>Bio <span style="font-size:11px; color:var(--muted); font-weight:400;">(max 500 characters)</span></label>
                <textarea name="bio" rows="4" maxlength="500" style="resize:vertical;">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')<div style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:10px;">Gender</label>
                <div style="display:flex; gap:20px; flex-wrap:wrap;">
                    @foreach(['male' => 'Male', 'female' => 'Female', 'prefer_not_to_say' => 'Prefer not to say'] as $val => $lbl)
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:400; font-size:14px; margin-bottom:0;">
                            <input type="radio" name="gender" value="{{ $val }}" {{ old('gender', $user->gender) === $val ? 'checked' : '' }}>
                            {{ $lbl }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="border:1px solid var(--border); border-radius:10px; overflow:hidden; margin-bottom:20px;">
                <label style="display:flex; align-items:flex-start; gap:12px; cursor:pointer; padding:14px 16px; border-bottom:1px solid var(--mid); margin-bottom:0; background:var(--white);">
                    <input type="checkbox" name="allow_dms" value="1" {{ old('allow_dms', $user->allow_dms) ? 'checked' : '' }} style="margin-top:3px;">
                    <div style="font-weight:400;">
                        <div style="font-size:14px; color:var(--black); font-weight:500;">Allow direct messages</div>
                        <div style="font-size:12px; color:var(--muted); margin-top:2px;">Other users can send you private messages</div>
                    </div>
                </label>
                <label style="display:flex; align-items:flex-start; gap:12px; cursor:pointer; padding:14px 16px; margin-bottom:0; background:var(--white);">
                    <input type="checkbox" name="hide_real_name" value="1" {{ old('hide_real_name', $user->hide_real_name) ? 'checked' : '' }} style="margin-top:3px;">
                    <div style="font-weight:400;">
                        <div style="font-size:14px; color:var(--black); font-weight:500;">Hide real name</div>
                        <div style="font-size:12px; color:var(--muted); margin-top:2px;">Only your username will be shown publicly</div>
                    </div>
                </label>
            </div>

            <button type="submit" style="width:100%;">Save Profile</button>
        </form>
    </div>
</div>

{{-- ── CHANGE PASSWORD ── --}}
<div class="card" style="padding:28px; margin-bottom:16px;">
    <h2 style="margin-bottom:20px;">Change Password</h2>
    <form method="POST" action="{{ route('user.password.update') }}" style="max-width:420px;">
        @csrf
        @method('PUT')
        <div style="margin-bottom:14px;">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
            @error('current_password')<div style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:14px;">
            <label>New Password</label>
            <input type="password" name="password" required>
            @error('password')<div style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:20px;">
            <label>Confirm New Password</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <button type="submit">Change Password</button>
    </form>
</div>

{{-- ── RATINGS ── --}}
<div class="card" style="padding:28px; margin-bottom:16px;">
    <h2 style="margin-bottom:20px;">My Ratings</h2>
    @if($ratings->isEmpty())
        <p style="color:var(--muted); font-size:14px;">You haven&apos;t rated any books yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ratings as $rating)
                    <tr>
                        <td><a href="{{ route('books.show', ['book' => $rating->book_id, 'back' => request()->fullUrl()]) }}">{{ $rating->book->title ?? 'Deleted Book' }}</a></td>
                        <td><span style="font-family:var(--font-mono); font-size:13px;">{{ $rating->rating }}/5</span></td>
                        <td style="color:var(--muted);">{{ $rating->comment ?: '—' }}</td>
                        <td style="color:var(--muted); font-size:12px; font-family:var(--font-mono); white-space:nowrap;">{{ $rating->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ── BORROW HISTORY ── --}}
<div class="card" style="padding:28px;">
    <h2 style="margin-bottom:20px;">Borrow History</h2>
    <table>
        <thead>
            <tr><th>Book</th><th>Borrowed</th><th>Returned</th><th>Status</th></tr>
        </thead>
        <tbody>
            @forelse($borrowHistory as $record)
                <tr>
                    <td>{{ $record->book->title ?? 'Deleted Book' }}</td>
                    <td style="font-family:var(--font-mono); font-size:12px; color:var(--muted); white-space:nowrap;">{{ $record->borrowed_at?->format('M d, Y') }}</td>
                    <td style="font-family:var(--font-mono); font-size:12px; color:var(--muted); white-space:nowrap;">{{ $record->returned_at?->format('M d, Y') ?? '—' }}</td>
                    <td>
                        @if($record->returned_at)
                            <span class="badge badge-green">Returned</span>
                        @else
                            <span class="badge">Active</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="color:var(--muted);">No borrow history yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection