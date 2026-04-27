@extends('layouts.app')

@section('title', 'Search')

@section('content')
    @php $currentUrl = request()->fullUrl(); @endphp

    <div class="card reveal">
        <h1 style="margin-bottom:6px;">Search</h1>
        <p style="color:var(--muted);margin-bottom:18px;font-size:15px;">Find books by title, author, or genre — and discover other readers.</p>
        <form method="GET" action="{{ route('search') }}" style="margin-top: 12px; display:flex; gap:14px; flex-wrap:wrap; align-items:end;">
            <div style="flex:1; min-width:220px;">
                <label style="font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;font-family:var(--font-mono);color:var(--muted);margin-bottom:7px;display:block;">Query</label>
                <input type="search" name="q" value="{{ $q }}" placeholder="Search books or users…" style="font-size:15px;padding:11px 14px;height:44px;">
            </div>
            <div style="min-width:150px;">
                <label style="font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;font-family:var(--font-mono);color:var(--muted);margin-bottom:7px;display:block;">Genre</label>
                <select name="genre" class="select-arrow" style="font-size:15px;padding:11px 14px;height:44px;">
                    <option value="">All Genres</option>
                    @foreach($genres as $g)
                        <option value="{{ $g }}" {{ request('genre') == $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:150px;">
                <label style="font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;font-family:var(--font-mono);color:var(--muted);margin-bottom:7px;display:block;">Availability</label>
                <select name="availability" class="select-arrow" style="font-size:15px;padding:11px 14px;height:44px;">
                    <option value="">All</option>
                    <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
            </div>
            <div style="min-width:160px;">
                <label style="font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;font-family:var(--font-mono);color:var(--muted);margin-bottom:7px;display:block;">Sort By</label>
                <select name="sort" class="select-arrow" style="font-size:15px;padding:11px 14px;height:44px;">
                    <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Newest Added</option>
                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title A – Z</option>
                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title Z – A</option>
                    <option value="year_asc" {{ request('sort') == 'year_asc' ? 'selected' : '' }}>Year Ascending</option>
                    <option value="year_desc" {{ request('sort') == 'year_desc' ? 'selected' : '' }}>Year Descending</option>
                </select>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" style="height:44px;padding:0 22px;font-size:14px;">Filter</button>
                <a href="{{ route('search') }}" class="btn-outline" style="height:44px;padding:0 18px;font-size:14px;display:flex;align-items:center;gap:6px;text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Books Results --}}
    <div class="reveal">
        <div class="section-header">
            <h2>Books</h2>
            @if($books->isNotEmpty())
                <span class="muted" style="font-size:13px;">{{ $books->count() }} result{{ $books->count() !== 1 ? 's' : '' }}</span>
            @endif
        </div>

        @if($q === '')
            <div class="card">
                <p class="muted">Type something to search books and users.</p>
            </div>
        @elseif($books->isEmpty())
            <div class="card">
                <div class="empty-state">
                    <div class="empty-state-icon">📚</div>
                    <div class="empty-state-title">No books found</div>
                    <div class="empty-state-desc">Try a different search term or filter.</div>
                </div>
            </div>
        @else
            <div class="grid grid-4">
                @foreach($books as $book)
                    <div class="book-card" onclick="window.location='{{ route('books.show', ['book' => $book->id, 'back' => $currentUrl]) }}'">
                        <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" loading="lazy"
                             onerror="this.onerror=null; this.src='{{ $book->noImageSvg() }}'">
                        <div class="book-card-body">
                            <div class="book-card-title">{{ $book->title }}</div>
                            <div class="book-card-author">{{ $book->author }}</div>
                            <div class="book-card-badges">
                                @if($book->genre)
                                    <span class="badge">{{ $book->genre }}</span>
                                @endif
                                <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}">
                                    {{ ucfirst($book->status) }}
                                </span>
                                <span class="badge" style="font-family:var(--font-mono);">{{ $book->published_year }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Users Results --}}
    <div class="reveal" style="margin-top:24px;">
        <div class="section-header">
            <h2>Users</h2>
            @if($users->isNotEmpty())
                <span class="muted" style="font-size:13px;">{{ $users->count() }} result{{ $users->count() !== 1 ? 's' : '' }}</span>
            @endif
        </div>

        @if($q === '')
            <div class="card">
                <p class="muted">User results will appear here too.</p>
            </div>
        @elseif($users->isEmpty())
            <div class="card">
                <div class="empty-state">
                    <div class="empty-state-icon">👤</div>
                    <div class="empty-state-title">No users found</div>
                    <div class="empty-state-desc">Try a different search term.</div>
                </div>
            </div>
        @else
            <div class="grid grid-3">
                @foreach($users as $user)
                    <a href="{{ route('user.public_profile', $user->id) }}" class="book-card" style="padding:20px; display:flex; align-items:center; gap:14px;">
                        <img src="{{ $user->avatarUrl() }}" style="width:56px;height:56px;border-radius:50%;object-fit:cover;flex-shrink:0; border:1.5px solid var(--border);">
                        <div style="min-width:0;">
                            <div style="font-weight:600; font-size:15px; color:var(--black); margin-bottom:3px;">{{ $user->username ?? $user->displayName() }}</div>
                            @php
                                $roleLabel = match($user->role) {
                                    'subscribed_user' => 'Subscriber',
                                    'admin' => 'Admin',
                                    'staff' => 'Staff',
                                    default => ucfirst($user->role),
                                };
                                $roleClass = match($user->role) {
                                    'admin' => 'role-admin',
                                    'staff' => 'role-staff',
                                    'subscribed_user' => 'role-subscribed',
                                    default => '',
                                };
                            @endphp
                            <span class="badge {{ $roleClass }}" style="margin-bottom:4px; display:inline-block;">{{ $roleLabel }}</span>
                            <div class="muted" style="font-size:12px; font-family:var(--font-mono);">Joined {{ $user->created_at?->format('M d, Y') ?? '—' }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
