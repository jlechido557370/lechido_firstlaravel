@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="card">
        <h1>Library Management System</h1>
        <p class="muted">Browse our collection. Borrow up to 5 books at a time, due in 10 days. Fines apply for overdue returns.</p>
        @guest
            <p>
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

        <form method="GET" action="{{ route('home') }}" data-autofilter style="margin-bottom: 12px;">
            <div class="row" style="flex-wrap: wrap; gap: 8px; align-items: center;">
                <input type="text" name="search" placeholder="Search title, author, genre, year…"
                       value="{{ request('search') }}"
                       style="flex: 2; min-width: 200px;"
                       onchange="this.form.submit()">

                <select name="genre" style="flex: 1; min-width: 140px;">
                    <option value="">All Genres</option>
                    @foreach($genres as $g)
                        <option value="{{ $g }}" {{ request('genre') === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>

                <select name="availability" style="flex: 1; min-width: 130px;">
                    <option value="">Any Availability</option>
                    <option value="available"   {{ request('availability') === 'available'   ? 'selected' : '' }}>Available Now</option>
                    <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>

                <select name="sort" style="flex: 1; min-width: 160px;">
                    <option value="latest"     {{ request('sort', 'latest') === 'latest'    ? 'selected' : '' }}>Newest Added</option>
                    <option value="year_desc"  {{ request('sort') === 'year_desc'           ? 'selected' : '' }}>Year: Newest First</option>
                    <option value="year_asc"   {{ request('sort') === 'year_asc'            ? 'selected' : '' }}>Year: Oldest First</option>
                    <option value="title_asc"  {{ request('sort') === 'title_asc'           ? 'selected' : '' }}>Title: A–Z</option>
                    <option value="title_desc" {{ request('sort') === 'title_desc'          ? 'selected' : '' }}>Title: Z–A</option>
                </select>

                @if(request('search') || request('genre') || request('availability') || (request('sort') && request('sort') !== 'latest'))
                    <a href="{{ route('home') }}" style="padding: 10px 14px; color: #666; white-space: nowrap; flex: 0;">Clear</a>
                @endif
            </div>
        </form>

        <p class="muted" style="margin-bottom: 12px;">
            Showing {{ $books->count() }} book(s)
            @if(request('genre')) in <strong>{{ request('genre') }}</strong>@endif
            @if(request('search')) matching "<strong>{{ request('search') }}</strong>"@endif
        </p>

        <div class="grid grid-2">
            @forelse ($books as $book)
                <a href="{{ route('books.show', $book->id) }}" style="text-decoration: none; color: inherit;">
                    <div class="card" style="cursor: pointer; margin-bottom: 0;"
                         onmouseover="this.style.borderColor='#111827'"
                         onmouseout="this.style.borderColor='#ddd'">
                        <strong>{{ $book->title }}</strong><br>
                        <span class="muted">{{ $book->author }}</span>
                        <div style="margin-top: 6px;">
                            <span class="badge">{{ $book->genre }}</span>
                            <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}" style="margin-left: 4px;">
                                {{ ucfirst($book->status) }}
                            </span>
                        </div>
                        <p class="muted" style="margin: 6px 0 0; font-size: 13px;">
                            {{ $book->published_year }} &bull; {{ $book->available_copies }}/{{ $book->total_copies }} copies
                        </p>
                        @if($book->description)
                            <p class="muted" style="font-size: 13px; margin: 6px 0 0; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                {{ $book->description }}
                            </p>
                        @endif
                    </div>
                </a>
            @empty
                <p>No books found.</p>
            @endforelse
        </div>
    </div>
@endsection