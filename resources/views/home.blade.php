@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <div class="card">
        <h1>Library Management System</h1>
        <p class="muted">Browse our collection. Borrow up to {{ auth()->check() && auth()->user()->isSubscribed() ? 25 : 5 }} books at a time, due in 10 days.</p>
        @guest
            <p><a href="{{ route('login') }}">Login</a> or <a href="{{ route('register') }}">Register</a> to borrow books.</p>
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
        <form method="GET" action="{{ route('home') }}" data-autofilter style="margin-bottom: 12px;">
            <div class="row" style="flex-wrap: wrap; gap: 8px; align-items: center;">
                <input type="text" name="search" placeholder="Search title, author, genre, ISBN, year..." value="{{ request('search') }}" style="flex: 2; min-width: 200px;" onchange="this.form.submit()">
                <select name="genre" style="flex: 1; min-width: 140px;">
                    <option value="">All Genres</option>
                    @foreach($genres as $g)
                        <option value="{{ $g }}" {{ request('genre') === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
                <select name="type" style="flex: 1; min-width: 120px;">
                    <option value="">All Types</option>
                    <option value="book"  {{ request('type') === 'book'  ? 'selected' : '' }}>Books</option>
                    <option value="manga" {{ request('type') === 'manga' ? 'selected' : '' }}>Manga</option>
                    <option value="comic" {{ request('type') === 'comic' ? 'selected' : '' }}>Comics</option>
                </select>
                <select name="availability" style="flex: 1; min-width: 130px;">
                    <option value="">Any Availability</option>
                    <option value="available"   {{ request('availability') === 'available'   ? 'selected' : '' }}>Available Now</option>
                    <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
                <select name="sort" style="flex: 1; min-width: 160px;">
                    <option value="latest"    {{ request('sort','latest') === 'latest'   ? 'selected' : '' }}>Newest Added</option>
                    <option value="year_desc" {{ request('sort') === 'year_desc'         ? 'selected' : '' }}>Year: Newest First</option>
                    <option value="year_asc"  {{ request('sort') === 'year_asc'          ? 'selected' : '' }}>Year: Oldest First</option>
                    <option value="title_asc" {{ request('sort') === 'title_asc'         ? 'selected' : '' }}>Title: A–Z</option>
                    <option value="title_desc"{{ request('sort') === 'title_desc'        ? 'selected' : '' }}>Title: Z–A</option>
                </select>
                @if(request('search') || request('genre') || request('availability') || request('type') || (request('sort') && request('sort') !== 'latest'))
                    <a href="{{ route('home') }}" style="padding: 10px 14px; color: #666; white-space: nowrap; flex: 0;">Clear</a>
                @endif
            </div>
        </form>

        <p class="muted" style="margin-bottom: 12px;">
            Showing {{ $books->count() }} book(s)
            @if(request('genre')) in <strong>{{ request('genre') }}</strong>@endif
            @if(request('search')) matching "<strong>{{ request('search') }}</strong>"@endif
        </p>

        {{-- BOOKS SECTION --}}
        <h2>Books</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px; margin-bottom: 32px;">
            @forelse ($books as $book)
                <a href="{{ route('books.show', ['book' => $book->id, 'back' => request()->fullUrl()]) }}" style="text-decoration: none; color: inherit;">
                    <div style="cursor: pointer; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: white; transition: border-color .15s;"
                         onmouseover="this.style.borderColor='#111827'" onmouseout="this.style.borderColor='#ddd'">
                        <img src="{{ $book->coverUrl() }}"
                             alt="{{ $book->title }}"
                             loading="lazy"
                             style="width: 100%; height: 200px; object-fit: cover; display: block; background: #e5e7eb;"
                             onerror="this.onerror=null; this.src='{{ $book->noImageSvg() }}'">
                        <div style="padding: 10px;">
                            <div style="font-weight: bold; font-size: 13px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; margin-bottom: 4px;">{{ $book->title }}</div>
                            <div class="muted" style="font-size: 12px; margin-bottom: 6px;">{{ $book->author }}</div>
                            <div>
                                @if($book->genre)
                                    <span class="badge" style="font-size: 11px; margin-right: 2px;">{{ $book->genre }}</span>
                                @endif
                                @if($book->showTypeBadge())
                                    <span class="badge" style="font-size: 11px; background: #e0e7ff;">{{ ucfirst($book->book_type) }}</span>
                                @endif
                            </div>
                            <div style="margin-top: 6px;">
                                <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}" style="font-size: 11px;">
                                    {{ ucfirst($book->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <p style="grid-column: 1/-1;">No books found.</p>
            @endforelse
        </div>

        <hr>

        {{-- COMICS SECTION --}}
        <h2>Comics</h2>
        @forelse($comicSeries as $series)
            <div style="display: inline-block; margin: 8px;">
                <a href="{{ route('series.show', $series) }}">{{ $series->title }}</a>
            </div>
        @empty
            <p>No comics available.</p>
        @endforelse

        <hr>

        {{-- MANGA SECTION --}}
        <h2>Manga</h2>
        @forelse($mangaSeries as $series)
            <div style="display: inline-block; margin: 8px;">
                <a href="{{ route('series.show', $series) }}">{{ $series->title }}</a>
            </div>
        @empty
            <p>No manga available.</p>
        @endforelse
    </div>
@endsection