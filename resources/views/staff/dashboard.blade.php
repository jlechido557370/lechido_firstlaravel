@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
    <div class="card">
        <h1>Staff Dashboard</h1>
        <p class="muted">Monitor and manage borrowings, reservations, users, and payment logs.</p>
        <div class="row">
            <a href="{{ route('staff.dashboard', ['section' => 'overview']) }}">Overview</a>
            <a href="{{ route('staff.dashboard', ['section' => 'borrowings']) }}">Borrowings</a>
            <a href="{{ route('staff.dashboard', ['section' => 'reservations']) }}">Reservations</a>
            <a href="{{ route('staff.dashboard', ['section' => 'users']) }}">Users</a>
            <a href="{{ route('staff.dashboard', ['section' => 'submissions']) }}">
                Submissions @if($stats['pending_books'] > 0)({{ $stats['pending_books'] }})@endif
            </a>
            <a href="{{ route('staff.dashboard', ['section' => 'payments']) }}">Payments</a>
            <a href="{{ route('staff.dashboard', ['section' => 'logs']) }}">Logs</a>
        </div>
    </div>

    @if($section === 'overview')
        <div class="grid grid-4">
            <div class="card"><div class="muted">Total Books</div><div class="stats">{{ $stats['total_books'] }}</div></div>
            <div class="card"><div class="muted">Active Borrows</div><div class="stats">{{ $stats['active_borrows'] }}</div></div>
            <div class="card"><div class="muted">Overdue</div><div class="stats">{{ $stats['overdue'] }}</div></div>
            <div class="card"><div class="muted">Total Users</div><div class="stats">{{ $stats['total_users'] }}</div></div>
        </div>
        <div class="card">
            <p class="muted">Pending unpaid fines: <strong>₱{{ number_format($stats['pending_fines'], 2) }}</strong></p>
        </div>
    @endif

    @if($section === 'borrowings')
        <div class="card">
            <h2>Borrowing Records</h2>
            <table>
                <thead>
                    <tr><th>User</th><th>Book</th><th>Borrowed</th><th>Due</th><th>Fine</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $borrowing)
                        <tr>
                            <td>{{ $borrowing->user->name ?? 'Unknown' }}</td>
                            <td>{{ $borrowing->book->title ?? 'Deleted' }}</td>
                            <td>{{ $borrowing->borrowed_at?->format('M d, Y') }}</td>
                            <td>{{ $borrowing->due_date?->format('M d, Y') }}</td>
                            <td>
                                @if($borrowing->fine_amount > 0)
                                    ₱{{ number_format($borrowing->fine_amount, 2) }}
                                    @if($borrowing->fine_paid)
                                        <span class="badge badge-green" style="font-size:11px;">Paid</span>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($borrowing->returned_at)
                                    <span class="badge badge-green">Returned</span>
                                @elseif($borrowing->is_overdue)
                                    <span class="badge badge-red">Overdue</span>
                                @else
                                    <span class="badge">Active</span>
                                @endif
                            </td>
                            <td>
                                @if(!$borrowing->returned_at)
                                    <form method="POST" action="{{ route('staff.borrowings.return', $borrowing->id) }}">
                                        @csrf
                                        <button type="submit" style="width:auto; padding:6px 10px;">Return</button>
                                    </form>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No borrowing records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($section === 'reservations')
        <div class="card">
            <h2>Reservations</h2>
            <table>
                <thead>
                    <tr><th>User</th><th>Book</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($reservations as $res)
                        <tr>
                            <td>{{ $res->user->name ?? 'Unknown' }}</td>
                            <td>{{ $res->book->title ?? 'Deleted' }}</td>
                            <td>
                                <span class="badge {{ $res->status === 'pending' ? '' : ($res->status === 'fulfilled' ? 'badge-green' : 'badge-red') }}">
                                    {{ ucfirst($res->status) }}
                                </span>
                            </td>
                            <td>{{ $res->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($res->status === 'pending')
                                    <form method="POST" action="{{ route('staff.reservations.fulfill', $res->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" style="width:auto; padding:6px 10px;">Fulfill</button>
                                    </form>
                                    <form method="POST" action="{{ route('staff.reservations.cancel', $res->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" style="width:auto; padding:6px 10px;">Cancel</button>
                                    </form>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No reservations.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($section === 'users')
        <div class="card">
            <h2>Users</h2>
            <table>
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Active Borrows</th><th>Overdue</th><th>Notify</th></tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $activeBorrows = $user->borrowRecords->whereNull('returned_at');
                            $overdueCount  = $activeBorrows->filter(fn($b) => $b->is_overdue)->count();
                        @endphp
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $activeBorrows->count() }}</td>
                            <td>
                                @if($overdueCount > 0)
                                    <span class="badge badge-red">{{ $overdueCount }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('staff.notify') }}" style="display:flex; gap:6px;">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <input type="text" name="message" placeholder="Message..." style="padding:4px 8px; font-size:13px; min-width:180px;">
                                    <button type="submit" style="width:auto; padding:4px 10px; font-size:13px;">Send</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No users.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($section === 'submissions')
        <div class="card">
            <h2>Book Submissions</h2>
            <table>
                <thead>
                    <tr><th>Cover</th><th>Title / Author</th><th>Submitted By</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                        <tr>
                            <td><img src="{{ $sub->coverUrl() }}" style="width:40px;height:55px;object-fit:cover;border-radius:4px;"></td>
                            <td>
                                {{ $sub->title }}<br>
                                <span class="muted">{{ $sub->author }}</span><br>
                                <span class="muted" style="font-size:12px;">{{ $sub->genre }}</span>
                                @if($sub->description)
                                    <div style="font-size:12px; color:#6b7280; margin-top:4px; max-width:300px;">{{ Str::limit($sub->description, 100) }}</div>
                                @endif
                            </td>
                            <td>{{ $sub->user->displayName() ?? 'Unknown' }}</td>
                            <td>
                                @if($sub->isPending())
                                    <span class="badge badge-yellow">Pending</span>
                                @elseif($sub->isApproved())
                                    <span class="badge badge-green">Approved</span>
                                @else
                                    <span class="badge badge-red">Rejected</span>
                                @endif
                            </td>
                            <td style="font-size:13px;">{{ $sub->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($sub->isPending())
                                    <form method="POST" action="{{ route('staff.submissions.approve', $sub->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" style="width:auto;padding:5px 10px;font-size:13px;background:#15803d;border-color:#15803d;">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('staff.submissions.reject', $sub->id) }}" style="display:inline;" onsubmit="return promptRejectReason(this)">
                                        @csrf
                                        <input type="hidden" name="rejection_reason" class="reject-reason-input">
                                        <button type="submit" style="width:auto;padding:5px 10px;font-size:13px;background:#dc2626;border-color:#dc2626;">Reject</button>
                                    </form>
                                @else
                                    <span class="muted" style="font-size:13px;">{{ $sub->rejection_reason ?? '' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No submissions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($section === 'payments')
        <div class="card">
            <h2>Payment Logs</h2>
            <table>
                <thead>
                    <tr><th>User</th><th>Book</th><th>Amount</th><th>Status</th><th>Finverse ID</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->user->name ?? 'Unknown' }}</td>
                            <td>{{ $payment->borrowRecord->book->title ?? 'Deleted' }}</td>
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
                            <td style="font-size:12px;">{{ $payment->finverse_payment_id ?? $payment->finverse_link_id ?? '—' }}</td>
                            <td>{{ $payment->paid_at?->format('M d, Y H:i') ?? $payment->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($section === 'logs')
        <div class="card">
            <h2>Activity Logs</h2>
            <table>
                <thead>
                    <tr><th>Time</th><th>User</th><th>Action</th><th>Description</th></tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="white-space:nowrap; font-size:13px;">{{ $log->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $log->user->name ?? 'System' }}</td>
                            <td><span class="badge">{{ str_replace('_', ' ', $log->action) }}</span></td>
                            <td>{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No logs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
@endsection

@push('scripts')
<script>
function promptRejectReason(form) {
    var reason = prompt('Enter rejection reason:');
    if (!reason || reason.trim() === '') return false;
    form.querySelector('.reject-reason-input').value = reason;
    return true;
}
</script>
@endpush