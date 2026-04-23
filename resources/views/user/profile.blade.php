@extends('layouts.app')

@section('title', 'My Profile — dotLibrary')

@section('content')

@push('scripts')
<style>
/* ── Profile page animations ── */
@keyframes profileFadeIn { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }
.profile-panel { animation: profileFadeIn .5s ease both; }
.profile-panel:nth-child(2) { animation-delay:.08s; }
.profile-panel:nth-child(3) { animation-delay:.14s; }

/* ── Radio pill buttons (same as registration) ── */
.radio-group { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px; }
.radio-option {
    display: flex; align-items: center; gap: 8px; cursor: pointer;
    padding: 9px 14px; border: 1.5px solid var(--border); border-radius: 9px;
    background: var(--off); transition: border-color .18s, background .15s;
    font-size: 13.5px; font-weight: 400; color: var(--black);
    user-select: none;
}
.radio-option:hover { border-color: var(--black); background: var(--white); opacity: 1; }
.radio-option input[type="radio"] {
    width: 16px !important; height: 16px !important;
    min-width: 16px !important; padding: 0 !important;
    margin: 0 !important; cursor: pointer; flex-shrink: 0;
    accent-color: var(--black);
    appearance: auto !important; -webkit-appearance: auto !important;
}
.radio-option:has(input:checked) {
    border-color: var(--black); background: var(--black); color: var(--white);
}

/* ── Checkbox option (same as registration) ── */
.checkbox-option {
    display: flex; align-items: flex-start; gap: 10px; cursor: pointer;
    padding: 14px 16px; border: 1.5px solid var(--border); border-radius: 9px;
    background: var(--off); transition: border-color .18s, background .15s;
    font-size: 13.5px; font-weight: 400; color: var(--black); line-height: 1.5;
    margin: 0;
}
.checkbox-option:hover { border-color: var(--black); background: var(--white); opacity: 1; }
.checkbox-option input[type="checkbox"] {
    width: 17px !important; height: 17px !important;
    min-width: 17px !important; padding: 0 !important;
    margin-top: 1px; cursor: pointer; flex-shrink: 0;
    accent-color: var(--black);
    appearance: auto !important; -webkit-appearance: auto !important;
}
.checkbox-option:has(input:checked) { border-color: var(--black); }

/* Fix Save Profile / Upload buttons in dark mode to always show clearly */
.profile-btn-primary {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: .02em;
    cursor: pointer;
    border: none;
    background: var(--black);
    color: var(--white);
    transition: opacity .18s, transform .1s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.profile-btn-primary:hover { opacity: .82; }
.profile-btn-primary:active { transform: scale(.98); }

.profile-btn-danger {
    width: 100%;
    padding: 11px;
    border-radius: 10px;
    font-size: 13.5px;
    cursor: pointer;
    border: 1.5px solid #dc2626;
    background: transparent;
    color: #dc2626;
    font-weight: 500;
    margin-top: 8px;
    transition: background .18s, color .18s;
}
.profile-btn-danger:hover { background: #dc2626; color: #fff; opacity: 1; }

.profile-btn-outline {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13px;
    cursor: pointer;
    border: 1.5px solid var(--border);
    background: transparent;
    color: var(--black);
    font-weight: 500;
    transition: border-color .18s, background .18s;
}
.profile-btn-outline:hover { border-color: var(--black); background: var(--off); opacity: 1; }


</style>
@endpush

{{-- ── HERO HEADER ── --}}
<div class="profile-panel" style="
    background: linear-gradient(135deg, #0f172a 0%, #111827 100%);
    color: #ffffff;
    padding: 36px 40px;
    border: 1px solid rgba(148,163,184,.18);
    margin-bottom: 16px;
    position: relative;
    overflow: hidden;
    border-radius: 16px;
">
    <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;border-radius:50%;border:1px solid rgba(255,255,255,.05);pointer-events:none;"></div>
    <div style="position:absolute;top:-20px;right:-20px;width:140px;height:140px;border-radius:50%;border:1px solid rgba(255,255,255,.07);pointer-events:none;"></div>

    <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
        <div style="position:relative;flex-shrink:0;">
            <img src="{{ $user->avatarUrl() }}" alt="avatar"
                 style="width:88px;height:88px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.25);box-shadow:0 4px 20px rgba(0,0,0,.4);">
            @if($user->isSubscribed())
                <div style="position:absolute;bottom:0;right:0;width:22px;height:22px;background:#ffffff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;border:2px solid #0f172a;">✦</div>
            @endif
        </div>
        <div style="flex:1;min-width:0;">
            <h1 style="font-size:26px;font-weight:500;color:#ffffff;margin-bottom:4px;">{{ $user->badgedName() }}</h1>
            <div style="font-size:12px;color:rgba(255,255,255,.68);font-family:var(--font-mono);letter-spacing:.04em;text-transform:uppercase;">
                {{ ucfirst($user->role) }} &nbsp;·&nbsp; Member since {{ $user->created_at->format('F Y') }}
            </div>
            @if($user->isSubscribed())
                <div style="display:inline-block;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);padding:3px 10px;font-size:11px;font-family:var(--font-mono);letter-spacing:.05em;color:rgba(255,255,255,.88);margin-top:8px;border-radius:4px;">
                    SUBSCRIBER — expires {{ $user->subscription_expires_at?->format('M d, Y') }}
                </div>
            @endif
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;flex-shrink:0;">
            <a href="{{ route('user.public_profile', $user->id) }}" style="color:rgba(255,255,255,.9);font-size:13px;border:1px solid rgba(255,255,255,.2);padding:8px 16px;border-radius:8px;display:inline-flex;align-items:center;gap:6px;transition:background .15s;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Public Profile
            </a>
            <a href="{{ route('user.ratings') }}" style="color:rgba(255,255,255,.9);font-size:13px;border:1px solid rgba(255,255,255,.2);padding:8px 16px;border-radius:8px;display:inline-flex;align-items:center;gap:6px;transition:background .15s;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                My Ratings
            </a>
        </div>
    </div>
</div>

{{-- ── EDIT PANELS ── --}}
<div class="grid grid-2" style="margin-bottom:16px;">

    {{-- Picture panel --}}
    <div class="card profile-panel" style="padding:28px;animation-delay:.1s;">
        <h2 style="margin-bottom:20px;font-size:16px;display:flex;align-items:center;gap:9px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Profile Picture
        </h2>

        <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid var(--mid);">
            <img src="{{ $user->avatarUrl() }}" alt="avatar"
                 style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:1.5px solid var(--border);flex-shrink:0;">
            <div>
                <div style="font-weight:600;font-size:15px;margin-bottom:2px;color:var(--black);">{{ $user->badgedName() }}</div>
                <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">{{ ucfirst($user->role) }} &nbsp;·&nbsp; Joined {{ $user->created_at->format('M Y') }}</div>
                @if($user->isSubscribed())
                    <div style="font-size:11px;color:var(--muted);font-family:var(--font-mono);">Subscriber — expires {{ $user->subscription_expires_at?->format('M d, Y') }}</div>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('user.avatar.update') }}" enctype="multipart/form-data">
            @csrf
            <label style="margin-bottom:8px;font-size:13px;font-weight:600;">Upload New Picture</label>
            <div style="font-size:12px;color:var(--muted);margin-bottom:10px;">JPG, PNG, GIF, WEBP &mdash; max 2MB</div>
            <input type="file" name="avatar" accept="image/*" required style="margin-bottom:14px;">
            @error('avatar')<div style="color:#dc2626;font-size:13px;margin-bottom:8px;">{{ $message }}</div>@enderror
            <button type="submit" class="profile-btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                Upload Picture
            </button>
        </form>

        @if($user->avatar)
            <form method="POST" action="{{ route('user.avatar.remove') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="profile-btn-danger">Remove Current Picture</button>
            </form>
        @endif
    </div>

    {{-- Update profile panel --}}
    <div class="card profile-panel" style="padding:28px;animation-delay:.18s;">
        <h2 style="margin-bottom:20px;font-size:16px;display:flex;align-items:center;gap:9px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Update Profile
        </h2>
        <form method="POST" action="{{ route('user.profile.update') }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">
                    Username
                    <span style="font-size:11px;color:var(--muted);font-family:var(--font-mono);font-weight:400;margin-left:6px;">(letters, numbers, underscores)</span>
                </label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required pattern="[a-zA-Z0-9_]+" minlength="3" maxlength="30">
                @error('username')<div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')<div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">
                    Bio
                    <span style="font-size:11px;color:var(--muted);font-weight:400;margin-left:6px;">(max 500 characters)</span>
                </label>
                <textarea name="bio" rows="4" maxlength="500" style="resize:vertical;">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')<div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            {{-- Gender --}}
            <div style="margin-bottom:20px;">
                <label style="font-size:13px;font-weight:600;margin-bottom:10px;display:block;">Gender</label>
                <div class="radio-group">
                    @foreach(['male' => 'Male', 'female' => 'Female', 'prefer_not_to_say' => 'Prefer not to say'] as $val => $lbl)
                        <label class="radio-option">
                            <input type="radio" name="gender" value="{{ $val }}" {{ old('gender', $user->gender) === $val ? 'checked' : '' }}>
                            {{ $lbl }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Toggles --}}
            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:22px;">
                <label class="checkbox-option">
                    <input type="checkbox" name="allow_dms" value="1" {{ old('allow_dms', $user->allow_dms) ? 'checked' : '' }}>
                    <div>
                        <div style="font-size:14px;font-weight:500;">Allow direct messages</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;">Other users can send you private messages</div>
                    </div>
                </label>
                <label class="checkbox-option">
                    <input type="checkbox" name="hide_real_name" value="1" {{ old('hide_real_name', $user->hide_real_name) ? 'checked' : '' }}>
                    <div>
                        <div style="font-size:14px;font-weight:500;">Hide real name</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;">Only your username will be shown publicly</div>
                    </div>
                </label>
            </div>

            <button type="submit" class="profile-btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Save Profile
            </button>
        </form>
    </div>
</div>

{{-- ── CHANGE PASSWORD ── --}}
<div class="card profile-panel" style="padding:28px;margin-bottom:16px;animation-delay:.22s;">
    <h2 style="margin-bottom:20px;font-size:16px;display:flex;align-items:center;gap:9px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Change Password
    </h2>
    <form method="POST" action="{{ route('user.password.update') }}" style="max-width:420px;">
        @csrf
        @method('PUT')
        <div style="margin-bottom:14px;">
            <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Current Password</label>
            <input type="password" name="current_password" required>
            @error('current_password')<div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:14px;">
            <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">New Password</label>
            <input type="password" name="password" required>
            @error('password')<div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:22px;">
            <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Confirm New Password</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <button type="submit" class="profile-btn-primary" style="width:auto;padding:11px 28px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Change Password
        </button>
    </form>
</div>

{{-- ── RATINGS ── --}}
<div class="card profile-panel" style="padding:28px;margin-bottom:16px;animation-delay:.28s;">
    <h2 style="margin-bottom:20px;font-size:16px;display:flex;align-items:center;gap:9px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        My Ratings
    </h2>
    @if($ratings->isEmpty())
        <p style="color:var(--muted);font-size:14px;">You haven&apos;t rated any books yet.</p>
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
                        <td><span style="font-family:var(--font-mono);font-size:13px;">{{ $rating->rating }}/5</span></td>
                        <td style="color:var(--muted);">{{ $rating->comment ?: '—' }}</td>
                        <td style="color:var(--muted);font-size:12px;font-family:var(--font-mono);white-space:nowrap;">{{ $rating->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ── BORROW HISTORY ── --}}
<div class="card profile-panel" style="padding:28px;animation-delay:.34s;">
    <h2 style="margin-bottom:20px;font-size:16px;display:flex;align-items:center;gap:9px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        Borrow History
    </h2>
    <table>
        <thead>
            <tr><th>Book</th><th>Borrowed</th><th>Returned</th><th>Status</th></tr>
        </thead>
        <tbody>
            @forelse($borrowHistory as $record)
                <tr>
                    <td>{{ $record->book->title ?? 'Deleted Book' }}</td>
                    <td style="font-family:var(--font-mono);font-size:12px;color:var(--muted);white-space:nowrap;">{{ $record->borrowed_at?->format('M d, Y') }}</td>
                    <td style="font-family:var(--font-mono);font-size:12px;color:var(--muted);white-space:nowrap;">{{ $record->returned_at?->format('M d, Y') ?? '—' }}</td>
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