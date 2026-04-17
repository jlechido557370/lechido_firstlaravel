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
                        <td><a href="{{ route('books.show', ['book' => $borrowing->book_id, 'back' => request()->fullUrl()]) }}">{{ $borrowing->book->title ?? 'Deleted Book' }}</a></td>
                        <td>{{ $borrowing->due_date?->format('M d, Y') }}</td>
                        <td>{{ $currentFine > 0 ? '₱'.number_format($currentFine, 2) : '—' }}</td>
                        <td>
                            @if($borrowing->is_overdue)
                                <span class="badge badge-red">Overdue</span>
                            @else
                                <span class="badge">Active</span>
                            @endif
                        </td>
                        <td style="display: flex; gap: 6px; flex-wrap: wrap;">
                            {{-- Read Now button — always shown for active borrows --}}
                            <a href="{{ route('books.read', ['book' => $borrowing->book_id, 'back' => request()->fullUrl()]) }}"
                               style="padding: 6px 10px; background: #15803d; color: white; border-radius: 6px; font-size: 13px; text-decoration: none; white-space: nowrap;">
                                Read Now
                            </a>
                            <form method="POST" action="{{ route('user.borrowings.return', $borrowing->id) }}" style="margin: 0;">
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
                        <td><a href="{{ route('books.show', ['book' => $res->book_id, 'back' => request()->fullUrl()]) }}">{{ $res->book->title ?? 'Deleted' }}</a></td>
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
        <h2>Borrowing History</h2>
        <table>
            <thead>
                <tr><th>Book</th><th>Borrowed</th><th>Returned</th><th>Fine Paid</th></tr>
            </thead>
            <tbody>
                @forelse($borrowingHistory as $record)
                    <tr>
                        <td><a href="{{ route('books.show', ['book' => $record->book_id, 'back' => request()->fullUrl()]) }}">{{ $record->book->title ?? 'Deleted Book' }}</a></td>
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