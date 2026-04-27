@extends('layouts.app')

@section('title', 'Staff Dashboard')

@push('scripts')
<style>
/* ── STAFF SHELL ── */
.staff-shell {
    display: flex;
    gap: 0;
    min-height: calc(100vh - 68px);
    margin: -36px;
}

/* ── SIDEBAR ── */
.staff-sidebar {
    width: 240px;
    flex-shrink: 0;
    background: var(--white);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 68px;
    height: calc(100vh - 68px);
    overflow-y: auto;
}
.staff-sidebar-head {
    padding: 24px 20px 16px;
    border-bottom: 1px solid var(--border);
}
.staff-sidebar-head-label {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    font-family: var(--font-mono);
    color: var(--muted);
    margin-bottom: 4px;
}
.staff-sidebar-head-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--black);
    font-family: var(--font-disp);
    letter-spacing: .04em;
}
.staff-sidebar-nav {
    padding: 10px 0;
    flex: 1;
}
.staff-nav-section {
    padding: 14px 20px 4px;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--muted);
    font-family: var(--font-mono);
}
.staff-nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    font-size: 13.5px;
    color: var(--black);
    border-radius: 0;
    transition: background .12s, color .12s, padding-left .15s;
    position: relative;
    text-decoration: none;
}
.staff-nav-link:hover {
    background: var(--off);
    padding-left: 26px;
    opacity: 1;
}
.staff-nav-link.active {
    background: var(--off);
    font-weight: 600;
}
.staff-nav-link.active::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: var(--black);
    border-radius: 0 2px 2px 0;
}
.staff-nav-link svg {
    width: 15px;
    height: 15px;
    stroke: currentColor;
    fill: none;
    stroke-width: 1.8;
    flex-shrink: 0;
    opacity: .6;
}
.staff-nav-badge {
    margin-left: auto;
    background: var(--black);
    color: var(--white);
    font-size: 9px;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    font-family: var(--font-mono);
    border-radius: 999px;
}

/* ── MAIN CONTENT ── */
.staff-main {
    flex: 1;
    min-width: 0;
    padding: 32px 36px;
    background: var(--bg-page);
}

/* ── PAGE HEADER ── */
.staff-page-header {
    margin-bottom: 28px;
}
.staff-page-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--black);
    margin-bottom: 4px;
}
.staff-page-sub {
    font-size: 13px;
    color: var(--muted);
}

/* ── STAT CARDS ── */
.staff-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.staff-stat-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 22px 20px;
    transition: box-shadow .2s, border-color .2s;
}
.staff-stat-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--black);
}
.staff-stat-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    font-family: var(--font-mono);
    color: var(--muted);
    margin-bottom: 10px;
}
.staff-stat-value {
    font-size: 36px;
    font-weight: 300;
    font-family: var(--font-disp);
    letter-spacing: .02em;
    color: var(--black);
    line-height: 1;
}
.staff-stat-sub {
    font-size: 12px;
    color: var(--muted);
    margin-top: 6px;
}
.staff-stat-card.accent {
    background: var(--black);
    border-color: var(--black);
}
.staff-stat-card.accent .staff-stat-label,
.staff-stat-card.accent .staff-stat-value,
.staff-stat-card.accent .staff-stat-sub {
    color: var(--white);
}
.staff-stat-card.accent .staff-stat-label { opacity: .6; }
.staff-stat-card.accent .staff-stat-sub   { opacity: .5; }

/* ── SECTION CARD ── */
.staff-section-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 14px;
    margin-bottom: 24px;
    overflow: hidden;
}
.staff-section-card-header {
    padding: 20px 24px 16px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.staff-section-card-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--black);
}
.staff-section-card-body {
    padding: 24px;
}

/* ── TABLE ── */
.staff-data-table { width: 100%; border-collapse: collapse; }
.staff-data-table th {
    font-size: 10px;
    letter-spacing: .1em;
    text-transform: uppercase;
    font-family: var(--font-mono);
    font-weight: 600;
    padding: 12px 16px;
    border-bottom: 2px solid var(--border);
    text-align: left;
    color: var(--muted);
    background: var(--off);
}
.staff-data-table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--mid);
    font-size: 13.5px;
    vertical-align: middle;
    color: var(--black);
}
.staff-data-table tr:last-child td { border-bottom: none; }
.staff-data-table tr:hover td { background: var(--off); }
.staff-data-table .cell-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }

/* ── INLINE ACTIONS ── */
.staff-table-actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
.staff-table-actions form { margin: 0; }
.staff-btn-sm {
    width: auto !important;
    padding: 5px 12px !important;
    font-size: 12px !important;
    border-radius: 6px !important;
}
.staff-btn-sm-danger {
    background: transparent !important;
    border-color: #dc2626 !important;
    color: #dc2626 !important;
}
.staff-btn-sm-danger:hover { background: rgba(220,38,38,.06) !important; opacity: 1 !important; }
.staff-btn-sm-green {
    background: #15803d !important;
    border-color: #15803d !important;
    color: #fff !important;
}
.staff-btn-sm-outline {
    background: transparent !important;
    color: var(--black) !important;
    border-color: var(--border) !important;
}
.staff-btn-sm-outline:hover { border-color: var(--black) !important; opacity: 1 !important; }

/* ── RESPONSIVE ── */
@media (max-width: 900px) {
    .staff-shell { flex-direction: column; margin: -36px; }
    .staff-sidebar { width: 100%; height: auto; position: static; flex-direction: row; overflow-x: auto; }
    .staff-sidebar-head { display: none; }
    .staff-sidebar-nav { display: flex; padding: 0; }
    .staff-nav-section { display: none; }
    .staff-nav-link { white-space: nowrap; padding: 14px 16px; }
    .staff-nav-link:hover { padding-left: 16px; }
    .staff-nav-link.active::before { top: auto; bottom: 0; left: 0; right: 0; width: auto; height: 3px; border-radius: 2px 2px 0 0; }
    .staff-main { padding: 20px; }
}
</style>
@endpush

@section('content')
<div class="staff-shell">

    {{-- ── SIDEBAR ── --}}
    <aside class="staff-sidebar">
        <div class="staff-sidebar-head">
            <div class="staff-sidebar-head-label">Control Panel</div>
            <div class="staff-sidebar-head-title">Staff</div>
        </div>
        <nav class="staff-sidebar-nav">
            <div class="staff-nav-section">Overview</div>
            <a href="{{ route('staff.dashboard', ['section' => 'overview']) }}"
               class="staff-nav-link {{ $section === 'overview' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Overview
            </a>

            <div class="staff-nav-section">Library</div>
            <a href="{{ route('staff.dashboard', ['section' => 'borrowings']) }}"
               class="staff-nav-link {{ $section === 'borrowings' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/><polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/></svg>
                Borrowings
            </a>
            <a href="{{ route('staff.dashboard', ['section' => 'reservations']) }}"
               class="staff-nav-link {{ $section === 'reservations' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                Reservations
            </a>
            <a href="{{ route('staff.dashboard', ['section' => 'submissions']) }}"
               class="staff-nav-link {{ $section === 'submissions' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Submissions
                @if($stats['pending_books'] > 0)
                    <span class="staff-nav-badge">{{ $stats['pending_books'] }}</span>
                @endif
            </a>

            <div class="staff-nav-section">Users</div>
            <a href="{{ route('staff.dashboard', ['section' => 'users']) }}"
               class="staff-nav-link {{ $section === 'users' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Users
            </a>

            <div class="staff-nav-section">Finance</div>
            <a href="{{ route('staff.dashboard', ['section' => 'payments']) }}"
               class="staff-nav-link {{ $section === 'payments' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Payments
            </a>

            <div class="staff-nav-section">System</div>
            <a href="{{ route('staff.dashboard', ['section' => 'logs']) }}"
               class="staff-nav-link {{ $section === 'logs' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Activity Logs
            </a>
        </nav>
    </aside>

    {{-- ── MAIN ── --}}
    <main class="staff-main">

        {{-- ── OVERVIEW ── --}}
        @if($section === 'overview')
        <div class="staff-page-header">
            <div class="staff-page-title">Overview</div>
            <div class="staff-page-sub">Library operations at a glance.</div>
        </div>

        <div class="staff-stat-grid">
            <div class="staff-stat-card accent">
                <div class="staff-stat-label">Total Books</div>
                <div class="staff-stat-value">{{ $stats['total_books'] }}</div>
                <div class="staff-stat-sub">In catalogue</div>
            </div>
            <div class="staff-stat-card">
                <div class="staff-stat-label">Active Borrows</div>
                <div class="staff-stat-value">{{ $stats['active_borrows'] }}</div>
                <div class="staff-stat-sub">Books currently out</div>
            </div>
            <div class="staff-stat-card">
                <div class="staff-stat-label">Overdue</div>
                <div class="staff-stat-value" style="color:{{ $stats['overdue'] > 0 ? '#b91c1c' : 'var(--black)' }};">{{ $stats['overdue'] }}</div>
                <div class="staff-stat-sub">Past due date</div>
            </div>
            <div class="staff-stat-card">
                <div class="staff-stat-label">Total Users</div>
                <div class="staff-stat-value">{{ $stats['total_users'] }}</div>
                <div class="staff-stat-sub">Registered accounts</div>
            </div>
            <div class="staff-stat-card">
                <div class="staff-stat-label">Pending Fines</div>
                <div class="staff-stat-value" style="font-size:22px; padding-top:8px;">₱{{ number_format($stats['pending_fines'], 2) }}</div>
                <div class="staff-stat-sub">Unpaid outstanding</div>
            </div>
            <div class="staff-stat-card">
                <div class="staff-stat-label">Submissions</div>
                <div class="staff-stat-value">{{ $stats['pending_books'] }}</div>
                <div class="staff-stat-sub">Awaiting review</div>
            </div>
        </div>

        <div class="staff-section-card">
            <div class="staff-section-card-header">
                <span class="staff-section-card-title">Quick Actions</span>
            </div>
            <div class="staff-section-card-body" style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('staff.dashboard', ['section' => 'borrowings']) }}" class="staff-btn-sm staff-btn-sm-outline" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/></svg>
                    Manage Borrowings
                </a>
                <a href="{{ route('staff.dashboard', ['section' => 'submissions']) }}" class="staff-btn-sm staff-btn-sm-outline" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Review Submissions
                </a>
                <a href="{{ route('staff.dashboard', ['section' => 'users']) }}" class="staff-btn-sm staff-btn-sm-outline" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    View Users
                </a>
            </div>
        </div>
        @endif

        {{-- ── BORROWINGS ── --}}
        @if($section === 'borrowings')
        <div class="staff-page-header">
            <div class="staff-page-title">Borrowing Records</div>
            <div class="staff-page-sub">All active and historical borrow records.</div>
        </div>

        <div class="staff-section-card">
            <table class="staff-data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Book</th>
                        <th>Borrowed</th>
                        <th>Due</th>
                        <th>Fine</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $borrowing)
                        <tr>
                            <td>{{ $borrowing->user->name ?? 'Unknown' }}</td>
                            <td>{{ $borrowing->book->title ?? 'Deleted' }}</td>
                            <td style="font-size:12px;white-space:nowrap;">{{ $borrowing->borrowed_at?->format('M d, Y') }}</td>
                            <td style="font-size:12px;white-space:nowrap;">{{ $borrowing->due_date?->format('M d, Y') }}</td>
                            <td>
                                @if($borrowing->fine_amount > 0)
                                    <span style="font-size:13px;">₱{{ number_format($borrowing->fine_amount, 2) }}</span>
                                    @if($borrowing->fine_paid)
                                        <span class="badge badge-green" style="font-size:10px;display:block;margin-top:3px;">Paid</span>
                                    @else
                                        <span class="badge badge-red" style="font-size:10px;display:block;margin-top:3px;">Unpaid</span>
                                    @endif
                                @else
                                    <span class="muted">—</span>
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
                                        <button type="submit" class="staff-btn-sm">Return</button>
                                    </form>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">📚</div>
                                <div class="empty-state-title">No borrowing records</div>
                                <div class="empty-state-desc">Records will appear when users borrow books.</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── RESERVATIONS ── --}}
        @if($section === 'reservations')
        <div class="staff-page-header">
            <div class="staff-page-title">Reservations</div>
            <div class="staff-page-sub">Manage book reservations and fulfillments.</div>
        </div>

        <div class="staff-section-card">
            <table class="staff-data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Book</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
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
                            <td style="font-size:12px;white-space:nowrap;">{{ $res->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($res->status === 'pending')
                                    <div class="staff-table-actions">
                                        <form method="POST" action="{{ route('staff.reservations.fulfill', $res->id) }}">
                                            @csrf
                                            <button type="submit" class="staff-btn-sm staff-btn-sm-green">Fulfill</button>
                                        </form>
                                        <form method="POST" action="{{ route('staff.reservations.cancel', $res->id) }}">
                                            @csrf
                                            <button type="submit" class="staff-btn-sm staff-btn-sm-danger">Cancel</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">
                            <div class="empty-state">
                                <div class="empty-state-icon">📋</div>
                                <div class="empty-state-title">No reservations</div>
                                <div class="empty-state-desc">Reservations will appear here when users request unavailable books.</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── USERS ── --}}
        @if($section === 'users')
        <div class="staff-page-header">
            <div class="staff-page-title">Users</div>
            <div class="staff-page-sub">Browse and notify library users.</div>
        </div>

        <div class="staff-section-card">
            <div class="staff-section-card-body" style="padding-bottom:16px;">
                <form method="GET" action="{{ route('staff.dashboard') }}" style="display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap;">
                    <input type="hidden" name="section" value="users">
                    <input type="text" name="user_search" value="{{ request('user_search') }}" placeholder="Search by ID, name, username, or email…" style="flex:1;min-width:200px;height:38px;font-size:13px;">
                    <button type="submit" class="staff-btn-sm" style="height:38px;">Search</button>
                    @if(request('user_search'))
                        <a href="{{ route('staff.dashboard', ['section' => 'users']) }}" class="staff-btn-sm staff-btn-sm-outline" style="height:38px;display:inline-flex;align-items:center;text-decoration:none;">Clear</a>
                    @endif
                </form>
            </div>
            <table class="staff-data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Active Borrows</th>
                        <th>Overdue</th>
                        <th>Notify</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $activeBorrows = $user->borrowRecords->whereNull('returned_at');
                            $overdueCount  = $activeBorrows->filter(fn($b) => $b->is_overdue)->count();
                        @endphp
                        <tr>
                            <td style="font-family:var(--font-mono);font-size:12px;color:var(--muted);">#{{ $user->id }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <img src="{{ $user->avatarUrl() }}" alt="" style="width:32px;height:32px;object-fit:cover;border:1px solid var(--border);border-radius:50%;flex-shrink:0;">
                                    <div>
                                        <strong>{{ $user->displayName() }}</strong>
                                        @if($user->name !== $user->username && $user->username)
                                            <div class="cell-sub">{{ $user->name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:12px; color:var(--muted);">{{ $user->email }}</td>
                            <td>{{ $activeBorrows->count() }}</td>
                            <td>
                                @if($overdueCount > 0)
                                    <span class="badge badge-red">{{ $overdueCount }}</span>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('staff.notify') }}" style="display:flex; gap:6px;">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <input type="text" name="message" placeholder="Message…" style="padding:4px 8px; font-size:13px; min-width:180px;">
                                    <button type="submit" class="staff-btn-sm">Send</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">👤</div>
                                <div class="empty-state-title">No users found</div>
                                <div class="empty-state-desc">Try adjusting your search.</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── SUBMISSIONS ── --}}
        @if($section === 'submissions')
        <div class="staff-page-header">
            <div class="staff-page-title">Book Submissions</div>
            <div class="staff-page-sub">Review and approve or reject user-submitted books.</div>
        </div>

        <div class="staff-section-card">
            <table class="staff-data-table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Book</th>
                        <th>Submitted By</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                        <tr>
                            <td><img src="{{ $sub->coverUrl() }}" style="width:36px;height:50px;object-fit:cover;border-radius:4px;border:1px solid var(--border);"></td>
                            <td>
                                <strong>{{ $sub->title }}</strong>
                                <div class="cell-sub">{{ $sub->author }}</div>
                                <div class="cell-sub">{{ $sub->genre }}</div>
                                @if($sub->description)
                                    <div style="font-size:12px;color:var(--muted);margin-top:4px;max-width:280px;">{{ Str::limit($sub->description, 90) }}</div>
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
                                    @if($sub->rejection_reason)
                                        <div class="cell-sub" style="max-width:160px;">{{ $sub->rejection_reason }}</div>
                                    @endif
                                @endif
                            </td>
                            <td style="font-size:12px;white-space:nowrap;">{{ $sub->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($sub->isPending())
                                    <div class="staff-table-actions">
                                        <form method="POST" action="{{ route('staff.submissions.approve', $sub->id) }}">
                                            @csrf
                                            <button type="submit" class="staff-btn-sm staff-btn-sm-green">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('staff.submissions.reject', $sub->id) }}" onsubmit="return promptRejectReason(this)">
                                            @csrf
                                            <input type="hidden" name="rejection_reason" class="reject-reason-input">
                                            <button type="submit" class="staff-btn-sm staff-btn-sm-danger">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">📄</div>
                                <div class="empty-state-title">No submissions</div>
                                <div class="empty-state-desc">User book submissions will appear here for review.</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── PAYMENTS ── --}}
        @if($section === 'payments')
        <div class="staff-page-header">
            <div class="staff-page-title">Payment Logs</div>
            <div class="staff-page-sub">All fine and subscription payment transactions.</div>
        </div>

        <div class="staff-section-card">
            <div class="staff-section-card-body" style="padding-bottom:16px;">
                <form method="GET" action="{{ route('staff.dashboard') }}" style="display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap;">
                    <input type="hidden" name="section" value="payments">
                    <input type="text" name="receipt_search" value="{{ request('receipt_search') }}" placeholder="Search by receipt ID, payment ID, or reference…" style="flex:1;min-width:200px;height:38px;font-size:13px;">
                    <button type="submit" class="staff-btn-sm" style="height:38px;">Search</button>
                    @if(request('receipt_search'))
                        <a href="{{ route('staff.dashboard', ['section' => 'payments']) }}" class="staff-btn-sm staff-btn-sm-outline" style="height:38px;display:inline-flex;align-items:center;text-decoration:none;">Clear</a>
                    @endif
                </form>
            </div>
            <table class="staff-data-table">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>User</th>
                        <th>Book</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Reference</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td style="font-family:var(--font-mono);font-size:12px;color:var(--muted);">#{{ $payment->id }}</td>
                            <td>{{ $payment->user->name ?? 'Unknown' }}</td>
                            <td>{{ $payment->borrowRecord->book->title ?? 'Deleted' }}</td>
                            <td style="font-family:var(--font-mono);font-size:13px;">₱{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                @if($payment->isCompleted())
                                    <span class="badge badge-green">Completed</span>
                                @elseif($payment->isPending())
                                    <span class="badge badge-yellow">Pending</span>
                                @else
                                    <span class="badge badge-red">{{ ucfirst($payment->status) }}</span>
                                @endif
                            </td>
                            <td style="font-size:11px;font-family:var(--font-mono);color:var(--muted);">{{ Str::limit($payment->finverse_payment_id ?? $payment->finverse_link_id ?? '—', 20) }}</td>
                            <td style="font-size:12px;white-space:nowrap;">{{ $payment->paid_at?->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">💳</div>
                                <div class="empty-state-title">No payments found</div>
                                <div class="empty-state-desc">Payment records will appear here.</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── LOGS ── --}}
        @if($section === 'logs')
        <div class="staff-page-header">
            <div class="staff-page-title">Activity Logs</div>
            <div class="staff-page-sub">Recent system actions and events.</div>
        </div>

        <div class="staff-section-card">
            <table class="staff-data-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="white-space:nowrap;font-size:12px;font-family:var(--font-mono);color:var(--muted);">{{ $log->created_at->format('M d, Y H:i') }}</td>
                            <td style="font-size:13px;">{{ $log->user->name ?? 'System' }}</td>
                            <td><span class="badge" style="font-size:10px;">{{ str_replace('_', ' ', $log->action) }}</span></td>
                            <td style="font-size:13px;">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">
                            <div class="empty-state">
                                <div class="empty-state-icon">📋</div>
                                <div class="empty-state-title">No logs yet</div>
                                <div class="empty-state-desc">System activity will be recorded here.</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

    </main>
</div>
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

