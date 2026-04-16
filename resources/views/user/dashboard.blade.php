@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
    <div class="card">
        <h1>User Dashboard</h1>
        <p class="muted">You can borrow up to 5 books at a time. Due in 10 days. Fine: ₱5/day overdue.</p>
        @if($totalFines > 0)
            <div class="flash error">You have outstanding fines totaling <strong>₱{{ number_format($totalFines, 2) }}</strong>.</div>
        @endif
    </div>

    <div class="grid grid-4">
        <div class="card"><div class="muted">Borrowed Now</div><div class="stats">{{ $stats['borrowed_now'] }} / 5</div></div>
        <div class="card"><div class="muted">Total Borrowed</div><div class="stats">{{ $stats['books_seen'] }}</div></div>
        <div class="card"><div class="muted">Returned</div><div class="stats">{{ $stats['returned'] }}</div></div>
        <div class="card"><div class="muted">Overdue</div><div class="stats">{{ $stats['overdue'] }}</div></div>
    </div>

    <div class="card">
        <h2>Current Borrowings</h2>
        <table>
            <thead>
                <tr><th>Book</th><th>Due Date</th><th>Fine</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse($currentBorrowings as $borrowing)
                    @php $currentFine = $borrowing->calculateFine(); @endphp
                    <tr>
                        <td><a href="{{ route('books.show', $borrowing->book_id) }}">{{ $borrowing->book->title ?? 'Deleted Book' }}</a></td>
                        <td>{{ $borrowing->due_date?->format('M d, Y') }}</td>
                        <td>{{ $currentFine > 0 ? '₱'.number_format($currentFine, 2) : '—' }}</td>
                        <td>
                            @if($borrowing->is_overdue)
                                <span class="badge badge-red">Overdue</span>
                            @else
                                <span class="badge">Active</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('user.borrowings.return', $borrowing->id) }}">
                                @csrf
                                <button type="submit" style="width:auto; padding:6px 10px;">Return</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No active borrowings.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reservations->count() > 0)
    <div class="card">
        <h2>My Reservations</h2>
        <table>
            <thead>
                <tr><th>Book</th><th>Reserved On</th><th>Action</th></tr>
            </thead>
            <tbody>
                @foreach($reservations as $res)
                    <tr>
                        <td><a href="{{ route('books.show', $res->book_id) }}">{{ $res->book->title ?? 'Deleted' }}</a></td>
                        <td>{{ $res->created_at->format('M d, Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('user.reservations.cancel', $res->id) }}">
                                @csrf
                                <button type="submit" style="width:auto; padding:6px 10px;">Cancel</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="card">
        <h2>Books</h2>

        <form method="GET" action="{{ route('user.dashboard') }}" data-autofilter style="margin-bottom: 12px;">
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
                    <a href="{{ route('user.dashboard') }}" style="padding: 10px 14px; color: #666; white-space: nowrap; flex: 0;">Clear</a>
                @endif
            </div>
        </form>

        <p class="muted" style="margin-bottom: 8px;">
            Showing {{ $books->count() }} book(s)
            @if(request('genre')) in <strong>{{ request('genre') }}</strong>@endif
            @if(request('search')) matching "<strong>{{ request('search') }}</strong>"@endif
        </p>

        <table>
            <thead>
                <tr><th>Book</th><th>Genre</th><th>Year</th><th>Copies</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse($books as $book)
                    @php
                        $alreadyBorrowed = $currentBorrowings->contains('book_id', $book->id);
                        $alreadyReserved = $reservations->contains('book_id', $book->id);
                        $atLimit = $stats['borrowed_now'] >= 5;
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('books.show', $book->id) }}">{{ $book->title }}</a><br>
                            <span class="muted">{{ $book->author }}</span>
                        </td>
                        <td>{{ $book->genre }}</td>
                        <td>{{ $book->published_year }}</td>
                        <td>{{ $book->available_copies }}/{{ $book->total_copies }}</td>
                        <td>{{ ucfirst($book->status) }}</td>
                        <td>
                            @if($alreadyBorrowed)
                                <span class="muted">Borrowed</span>
                            @elseif($book->available_copies > 0 && !$atLimit)
                                <form method="POST" action="{{ route('books.borrow', $book->id) }}">
                                    @csrf
                                    <button type="submit" style="width:auto; padding:6px 10px;">Borrow</button>
                                </form>
                            @elseif($atLimit && $book->available_copies > 0)
                                <span class="muted">Limit reached</span>
                            @elseif($book->available_copies <= 0 && !$alreadyReserved)
                                <form method="POST" action="{{ route('books.reserve', $book->id) }}">
                                    @csrf
                                    <button type="submit" style="width:auto; padding:6px 10px;">Reserve</button>
                                </form>
                            @elseif($alreadyReserved)
                                <span class="muted">Reserved</span>
                            @else
                                <span class="muted">Unavailable</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No books found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Borrowing History</h2>
        <table>
            <thead>
                <tr><th>Book</th><th>Borrowed</th><th>Returned</th><th>Fine Paid</th></tr>
            </thead>
            <tbody>
                @forelse($borrowingHistory as $record)
                    <tr>
                        <td><a href="{{ route('books.show', $record->book_id) }}">{{ $record->book->title ?? 'Deleted Book' }}</a></td>
                        <td>{{ $record->borrowed_at?->format('M d, Y') }}</td>
                        <td>{{ $record->returned_at?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $record->fine_amount > 0 ? '₱'.number_format($record->fine_amount, 2) : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No history yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection