@extends('layouts.app')
@section('title', 'Home')

@section('content')
    <div class="card">
        <h1>Library Management System</h1>
        <p class="muted">
            Browse our collection. Borrow up to {{ auth()->check() && auth()->user()->isSubscribed() ? 25 : 5 }} books at a time, due in 10 days.
        </p>

        @guest
            <p style="margin-top:10px;">
                <a href="{{ route('login') }}">Login</a> or
                <a href="{{ route('register') }}">Register</a> to borrow books.
            </p>
        @endguest
    </div>

    <div class="grid grid-4">
        <div class="card"><div class="muted">Total Books</div><div class="stats">{{ $stats['total_books'] }}</div></div>
        <div class="card"><div class="muted">Available Copies</div><div class="stats">{{ $stats['available_books'] }}</div></div>
        <div class="card"><div class="muted">Active Borrows</div><div class="stats">{{ $stats['active_borrows'] }}</div></div>
        <div class="card"><div class="muted">Members</div><div class="stats">{{ $stats['members'] }}</div></div>
    </div>

    <div class="card">
        <h2>Browse Books</h2>

        {{-- FILTERS --}}
        <form method="GET" action="{{ route('home') }}" data-autofilter style="margin-bottom:16px;">
            <div class="row" style="flex-wrap:wrap; gap:10px; align-items:center;">
                
                <input type="text" name="search"
                    placeholder="Search title, author, genre, ISBN, year..."
                    value="{{ request('search') }}"
                    style="flex:2; min-width:200px;"
                    onchange="this.form.submit()">

                <select name="genre" style="flex:1; min-width:140px;">
                    <option value="">All Genres</option>
                    @foreach($genres as $g)
                        <option value="{{ $g }}" {{ request('genre') === $g ? 'selected' : '' }}>
                            {{ $g }}
                        </option>
                    @endforeach
                </select>

                {{-- ❌ TYPE FILTER REMOVED --}}

                <select name="availability" style="flex:1; min-width:130px;">
                    <option value="">Any Availability</option>
                    <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>
                        Available Now
                    </option>
                    <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>
                        Unavailable
                    </option>
                </select>

                <select name="sort" style="flex:1; min-width:160px;">
                    <option value="latest" {{ request('sort','latest') === 'latest' ? 'selected' : '' }}>
                        Newest Added
                    </option>
                    <option value="year_desc" {{ request('sort') === 'year_desc' ? 'selected' : '' }}>
                        Year: Newest First
                    </option>
                    <option value="year_asc" {{ request('sort') === 'year_asc' ? 'selected' : '' }}>
                        Year: Oldest First
                    </option>
                    <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>
                        Title: A–Z
                    </option>
                    <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>
                        Title: Z–A
                    </option>
                </select>

                @if(request('search') || request('genre') || request('availability') || (request('sort') && request('sort') !== 'latest'))
                    <a href="{{ route('home') }}"
                       style="padding:10px 14px; color:var(--muted); white-space:nowrap; border:1.5px solid var(--border); border-radius:8px; font-size:13px;">
                        Clear
                    </a>
                @endif
            </div>
        </form>

        {{-- RESULT COUNT --}}
        <p class="muted" style="margin-bottom:16px;">
            Showing {{ $books->count() }} book(s)
            @if(request('genre')) in <strong>{{ request('genre') }}</strong>@endif
            @if(request('search')) matching “<strong>{{ request('search') }}</strong>”@endif
        </p>

        {{-- BOOKS ONLY --}}
        <h2>Books</h2>

        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:18px;">
            @forelse ($books as $book)
                <a href="{{ route('books.show', ['book' => $book->id, 'back' => request()->fullUrl()]) }}" class="book-card">
                    
                    <img src="{{ $book->coverUrl() }}"
                         alt="{{ $book->title }}"
                         loading="lazy"
                         onerror="this.onerror=null; this.src='{{ $book->noImageSvg() }}'">

                    <div class="book-card-body">
                        <div class="book-card-title">{{ $book->title }}</div>
                        <div class="book-card-author">{{ $book->author }}</div>

                        <div class="book-card-badges">
                            @if($book->genre)
                                <span class="badge" style="font-size:11px;">
                                    {{ $book->genre }}
                                </span>
                            @endif

                            <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}"
                                  style="font-size:11px;">
                                {{ ucfirst($book->status) }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <p style="grid-column:1/-1;color:var(--muted);">
                    No books found.
                </p>
            @endforelse
        </div>

    </div>
@endsection