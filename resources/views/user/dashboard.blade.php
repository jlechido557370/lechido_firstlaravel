@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
    <div class="card">
        <h1>User Dashboard</h1>
        <p class="muted">You can borrow up to 5 books at a time. Due in 10 days. Fine: ₱5/day overdue.</p>
        @if($totalFines > 0)
            <div class="flash error">You have outstanding unpaid fines totaling <strong>₱{{ number_format($totalFines, 2) }}</strong>. Pay your fines to return books.</div>
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
                <tr><th>Book</th><th>Due Date</th><th>Fine</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($currentBorrowings as $borrowing)
                    @php
                        $currentFine    = $borrowing->calculateFine();
                        $hasPendingPay  = $borrowing->hasPendingPayment();
                        $isDueSoon      = $borrowing->is_due_soon;
                    @endphp
                    <tr @if($borrowing->is_overdue) style="background:#fff5f5;" @elseif($isDueSoon) style="background:#fffbeb;" @endif>
                        <td>
                            <a href="{{ route('books.show', ['book' => $borrowing->book_id]) }}">{{ $borrowing->book->title ?? 'Deleted Book' }}</a>
                            @if($isDueSoon && !$borrowing->is_overdue)
                                <br><span style="color:#92400e; font-size:12px;">Due in less than 24 hours</span>
                            @endif
                        </td>
                        <td>{{ $borrowing->due_date?->format('M d, Y') }}</td>
                        <td>
                            @if($currentFine > 0)
                                <strong style="color:#dc2626;">₱{{ number_format($currentFine, 2) }}</strong>
                                @if($borrowing->fine_paid)
                                    <br><span class="badge badge-green" style="font-size:11px;">Paid</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($borrowing->is_overdue)
                                <span class="badge badge-red">Overdue</span>
                            @elseif($isDueSoon)
                                <span class="badge badge-yellow">Due Soon</span>
                            @else
                                <span class="badge">Active</span>
                            @endif
                        </td>
                        <td style="display: flex; gap: 6px; flex-wrap: wrap;">
                            <a href="{{ route('books.read', ['book' => $borrowing->book_id]) }}"
                               style="padding: 6px 10px; background: #15803d; color: white; border-radius: 6px; font-size: 13px; text-decoration: none; white-space: nowrap;">
                                Read Now
                            </a>

                            @if($currentFine > 0 && !$borrowing->fine_paid)
                                @if($hasPendingPay)
                                    <span style="padding:6px 10px; font-size:13px; color:#92400e;">Payment pending...</span>
                                @else
                                    <form method="POST" action="{{ route('payments.initiate', $borrowing->id) }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" style="width:auto; padding:6px 10px; background:#dc2626; border-color:#dc2626;">
                                            Pay ₱{{ number_format($currentFine, 2) }}
                                        </button>
                                    </form>
                                @endif
                            @else
                                <form method="POST" action="{{ route('user.borrowings.return', $borrowing->id) }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="width:auto; padding:6px 10px;">Return</button>
                                </form>
                            @endif
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
                        <td><a href="{{ route('books.show', ['book' => $res->book_id]) }}">{{ $res->book->title ?? 'Deleted' }}</a></td>
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
                <tr><th>Book</th><th>Borrowed</th><th>Returned</th><th>Fine</th><th>Fine Status</th></tr>
            </thead>
            <tbody>
                @forelse($borrowingHistory as $record)
                    <tr>
                        <td><a href="{{ route('books.show', ['book' => $record->book_id]) }}">{{ $record->book->title ?? 'Deleted Book' }}</a></td>
                        <td>{{ $record->borrowed_at?->format('M d, Y') }}</td>
                        <td>{{ $record->returned_at?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $record->fine_amount > 0 ? '₱'.number_format($record->fine_amount, 2) : '—' }}</td>
                        <td>
                            @if($record->fine_amount > 0)
                                @if($record->fine_paid)
                                    <span class="badge badge-green">Paid</span>
                                @else
                                    <span class="badge badge-red">Unpaid</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No history yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Payment History</h2>
        <table>
            <thead>
                <tr><th>Book</th><th>Amount</th><th>Status</th><th>Date</th></tr>
            </thead>
            <tbody>
                @forelse($paymentLogs as $payment)
                    <tr>
                        <td>{{ $payment->borrowRecord->book->title ?? 'Deleted Book' }}</td>
                        <td>₱{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            @if($payment->isCompleted())
                                <span class="badge badge-green">Completed</span>
                            @elseif($payment->isPending())
                                <span class="badge badge-yellow">Pending</span>
                            @else
                                <span class="badge badge-red">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $payment->paid_at?->format('M d, Y H:i') ?? $payment->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No payments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection