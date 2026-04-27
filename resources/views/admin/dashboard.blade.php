@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('scripts')
<style>
/* ── ADMIN SHELL ── */
.admin-shell {
    display: flex;
    gap: 0;
    min-height: calc(100vh - 68px);
    margin: -36px;
}

/* ── SIDEBAR ── */
.admin-sidebar {
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
.admin-sidebar-head {
    padding: 24px 20px 16px;
    border-bottom: 1px solid var(--border);
}
.admin-sidebar-head-label {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    font-family: var(--font-mono);
    color: var(--muted);
    margin-bottom: 4px;
}
.admin-sidebar-head-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--black);
    font-family: var(--font-disp);
    letter-spacing: .04em;
}
.admin-sidebar-nav {
    padding: 10px 0;
    flex: 1;
}
.admin-nav-section {
    padding: 14px 20px 4px;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--muted);
    font-family: var(--font-mono);
}
.admin-nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    font-size: 13.5px;
    color: var(--black);
    border-radius: 0;
    transition: background .12s, color .12s, padding-left .15s;
    position: relative;
}
.admin-nav-link:hover {
    background: var(--off);
    padding-left: 26px;
    opacity: 1;
}
.admin-nav-link.active {
    background: var(--off);
    font-weight: 600;
}
.admin-nav-link.active::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: var(--black);
    border-radius: 0 2px 2px 0;
}
.admin-nav-link svg {
    width: 15px;
    height: 15px;
    stroke: currentColor;
    fill: none;
    stroke-width: 1.8;
    flex-shrink: 0;
    opacity: .6;
}
.admin-nav-badge {
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
.admin-main {
    flex: 1;
    min-width: 0;
    padding: 32px 36px;
    background: var(--bg-page);
}

/* ── PAGE HEADER ── */
.admin-page-header {
    margin-bottom: 28px;
}
.admin-page-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--black);
    margin-bottom: 4px;
}
.admin-page-sub {
    font-size: 13px;
    color: var(--muted);
}

/* ── STAT CARDS ── */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.stat-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 22px 20px;
    transition: box-shadow .2s, border-color .2s;
}
.stat-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--black);
}
.stat-card-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    font-family: var(--font-mono);
    color: var(--muted);
    margin-bottom: 10px;
}
.stat-card-value {
    font-size: 36px;
    font-weight: 300;
    font-family: var(--font-disp);
    letter-spacing: .02em;
    color: var(--black);
    line-height: 1;
}
.stat-card-sub {
    font-size: 12px;
    color: var(--muted);
    margin-top: 6px;
}
.stat-card.accent {
    background: var(--black);
    border-color: var(--black);
}
.stat-card.accent .stat-card-label,
.stat-card.accent .stat-card-value,
.stat-card.accent .stat-card-sub {
    color: var(--white);
}
.stat-card.accent .stat-card-label { opacity: .6; }
.stat-card.accent .stat-card-sub   { opacity: .5; }

/* ── SECTION CARD ── */
.section-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 14px;
    margin-bottom: 24px;
    overflow: hidden;
}
.section-card-header {
    padding: 20px 24px 16px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.section-card-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--black);
}
.section-card-body {
    padding: 24px;
}

/* ── FORM GRID ── */
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
.form-field { display: flex; flex-direction: column; gap: 6px; }
.form-field label { font-size: 12px; font-weight: 600; letter-spacing: .04em; color: var(--muted); text-transform: uppercase; font-family: var(--font-mono); }
.form-full { grid-column: 1 / -1; }
.form-actions { display: flex; gap: 10px; align-items: center; padding-top: 8px; }
.form-actions button { width: auto; }

/* ── TABLE ── */
.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
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
.data-table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--mid);
    font-size: 13.5px;
    vertical-align: middle;
    color: var(--black);
}
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: var(--off); }
.data-table .cell-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }

/* ── INLINE FORM ACTIONS ── */
.table-actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
.table-actions form { margin: 0; }
.btn-sm {
    width: auto !important;
    padding: 5px 12px !important;
    font-size: 12px !important;
    border-radius: 6px !important;
}
.btn-sm-danger {
    background: transparent !important;
    border-color: #dc2626 !important;
    color: #dc2626 !important;
}
.btn-sm-danger:hover { background: rgba(220,38,38,.06) !important; opacity: 1 !important; }
.btn-sm-green {
    background: #15803d !important;
    border-color: #15803d !important;
    color: #fff !important;
}
.btn-sm-outline {
    background: transparent !important;
    color: var(--black) !important;
    border-color: var(--border) !important;
}
.btn-sm-outline:hover { border-color: var(--black) !important; opacity: 1 !important; }

/* ── ROLE BADGE COLORS ── */
.role-admin    { background: rgba(109,40,217,.12); color: #6d28d9; }
.role-staff    { background: rgba(2,132,199,.12);  color: #0284c7; }
.role-subscribed { background: rgba(217,119,6,.12); color: #b45309; }
.role-user     { background: var(--mid); color: var(--black); }
[data-theme="dark"] .role-admin    { color: #a78bfa; }
[data-theme="dark"] .role-staff    { color: #38bdf8; }
[data-theme="dark"] .role-subscribed { color: #fbbf24; }

/* ── EMPTY STATE ── */
.empty-state {
    padding: 48px 24px;
    text-align: center;
    color: var(--muted);
    font-size: 14px;
}
.empty-state-icon {
    font-size: 32px;
    margin-bottom: 12px;
    opacity: .4;
}

/* ── RESPONSIVE ── */
@media (max-width: 900px) {
    .admin-shell { flex-direction: column; margin: -36px; }
    .admin-sidebar { width: 100%; height: auto; position: static; flex-direction: row; overflow-x: auto; }
    .admin-sidebar-head { display: none; }
    .admin-sidebar-nav { display: flex; padding: 0; }
    .admin-nav-section { display: none; }
    .admin-nav-link { white-space: nowrap; padding: 14px 16px; }
    .admin-nav-link:hover { padding-left: 16px; }
    .admin-nav-link.active::before { top: auto; bottom: 0; left: 0; right: 0; width: auto; height: 3px; border-radius: 2px 2px 0 0; }
    .admin-main { padding: 20px; }
    .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="admin-shell">

    {{-- ── SIDEBAR ── --}}
    <aside class="admin-sidebar">
        <div class="admin-sidebar-head">
            <div class="admin-sidebar-head-label">Control Panel</div>
            <div class="admin-sidebar-head-title">Admin</div>
        </div>
        <nav class="admin-sidebar-nav">
            <div class="admin-nav-section">Overview</div>
            <a href="{{ route('admin.dashboard', ['section' => 'overview']) }}"
               class="admin-nav-link {{ $section === 'overview' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Overview
            </a>

            <div class="admin-nav-section">Library</div>
            <a href="{{ route('admin.dashboard', ['section' => 'books']) }}"
               class="admin-nav-link {{ $section === 'books' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Books
            </a>
            <a href="{{ route('admin.dashboard', ['section' => 'borrowings']) }}"
               class="admin-nav-link {{ $section === 'borrowings' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/><polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/></svg>
                Borrowings
            </a>
            <a href="{{ route('admin.dashboard', ['section' => 'submissions']) }}"
               class="admin-nav-link {{ $section === 'submissions' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Submissions
                @if($stats['pending_books'] > 0)
                    <span class="admin-nav-badge">{{ $stats['pending_books'] }}</span>
                @endif
            </a>

            <div class="admin-nav-section">Users</div>
            <a href="{{ route('admin.dashboard', ['section' => 'users']) }}"
               class="admin-nav-link {{ $section === 'users' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Users &amp; Roles
            </a>

            <div class="admin-nav-section">Finance</div>
            <a href="{{ route('admin.dashboard', ['section' => 'payments']) }}"
               class="admin-nav-link {{ $section === 'payments' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Payments
            </a>

            <div class="admin-nav-section">System</div>
            <a href="{{ route('admin.dashboard', ['section' => 'logs']) }}"
               class="admin-nav-link {{ $section === 'logs' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Activity Logs
            </a>
        </nav>
    </aside>

    {{-- ── MAIN ── --}}
    <main class="admin-main">

        {{-- ── OVERVIEW ── --}}
        @if($section === 'overview')
        <div class="admin-page-header">
            <div class="admin-page-title">Overview</div>
            <div class="admin-page-sub">System-wide statistics at a glance.</div>
        </div>

        <div class="stat-grid">
            <div class="stat-card accent">
                <div class="stat-card-label">Total Books</div>
                <div class="stat-card-value">{{ $stats['total_books'] }}</div>
                <div class="stat-card-sub">{{ $stats['available_copies'] }} copies available</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Active Borrows</div>
                <div class="stat-card-value">{{ $stats['active_borrows'] }}</div>
                <div class="stat-card-sub">Books currently out</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Overdue</div>
                <div class="stat-card-value" style="color:{{ $stats['overdue'] > 0 ? '#b91c1c' : 'var(--black)' }};">{{ $stats['overdue'] }}</div>
                <div class="stat-card-sub">Past due date</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Total Users</div>
                <div class="stat-card-value">{{ $stats['total_users'] }}</div>
                <div class="stat-card-sub">Registered accounts</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Pending Fines</div>
                <div class="stat-card-value" style="font-size:22px; padding-top:8px;">₱{{ number_format($stats['pending_fines'], 2) }}</div>
                <div class="stat-card-sub">Unpaid outstanding</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Submissions</div>
                <div class="stat-card-value">{{ $stats['pending_books'] }}</div>
                <div class="stat-card-sub">Awaiting review</div>
            </div>
        </div>
        @endif

        {{-- ── BOOKS ── --}}
        @if($section === 'books')
        <div class="admin-page-header">
            <div class="admin-page-title">{{ $editingBook ? 'Edit Book' : 'Add a Book' }}</div>
            <div class="admin-page-sub">{{ $editingBook ? 'Update the details for this book.' : 'Add a new book to the library catalogue.' }}</div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">{{ $editingBook ? 'Editing: ' . $editingBook->title : 'New Book' }}</span>
                @if($editingBook)
                    <a href="{{ route('admin.dashboard', ['section' => 'books']) }}" class="btn-sm btn-sm-outline" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border:1.5px solid var(--border); border-radius:6px; font-size:12px; color:var(--black);">Cancel</a>
                @endif
            </div>
            <div class="section-card-body">
                <form method="POST" action="{{ $editingBook ? route('admin.books.update', $editingBook->id) : route('admin.books.store') }}">
                    @csrf
                    @if($editingBook) @method('PUT') @endif

                    <div class="form-grid-2" style="margin-bottom:16px;">
                        <div class="form-field">
                            <label>Title</label>
                            <input type="text" name="title" value="{{ old('title', $editingBook->title ?? '') }}" required placeholder="Book title">
                        </div>
                        <div class="form-field">
                            <label>Author</label>
                            <input type="text" name="author" value="{{ old('author', $editingBook->author ?? '') }}" required placeholder="Author name">
                        </div>
                        <div class="form-field">
                            <label>ISBN</label>
                            <input type="text" name="isbn" value="{{ old('isbn', $editingBook->isbn ?? '') }}" required placeholder="e.g. 9780132350884">
                        </div>
                        <div class="form-field">
                            <label>Genre</label>
                            <input type="text" name="genre" value="{{ old('genre', $editingBook->genre ?? '') }}" required placeholder="e.g. Fiction">
                        </div>
                        <div class="form-field">
                            <label>Published Year</label>
                            <input type="number" name="published_year" value="{{ old('published_year', $editingBook->published_year ?? '') }}" required placeholder="{{ date('Y') }}">
                        </div>
                        <div class="form-field">
                            <label>Total Copies</label>
                            <input type="number" name="total_copies" min="1" value="{{ old('total_copies', $editingBook->total_copies ?? 1) }}" required>
                        </div>
                        <div class="form-field form-full">
                            <label>Description</label>
                            <textarea name="description" rows="3" placeholder="Brief description of the book...">{{ old('description', $editingBook->description ?? '') }}</textarea>
                        </div>
                        <div class="form-field form-full">
                            <label>Read URL (optional)</label>
                            <input type="url" name="read_url" placeholder="https://..." value="{{ old('read_url', $editingBook->read_url ?? '') }}">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit">{{ $editingBook ? 'Update Book' : 'Save Book' }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">Book Catalogue</span>
                <span class="badge" style="font-size:11px;">{{ $books->count() }} books</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Copies</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>
                                <strong>{{ $book->title }}</strong>
                                <div class="cell-sub">{{ $book->author }}</div>
                            </td>
                            <td><span class="badge">{{ $book->genre }}</span></td>
                            <td>
                                <strong>{{ $book->available_copies }}</strong><span class="muted"> / {{ $book->total_copies }}</span>
                            </td>
                            <td><span class="badge {{ $book->available_copies > 0 ? 'badge-green' : 'badge-red' }}">{{ $book->available_copies > 0 ? 'Available' : 'All Out' }}</span></td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.dashboard', ['section' => 'books', 'edit' => $book->id]) }}"
                                       class="btn-sm btn-sm-outline" style="text-decoration:none;display:inline-flex;align-items:center;padding:5px 12px;border:1.5px solid var(--border);border-radius:6px;font-size:12px;color:var(--black);">Edit</a>
                                    <form method="POST" action="{{ route('admin.books.destroy', $book->id) }}" onsubmit="return confirm('Delete this book?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-sm btn-sm-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state">No books yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── BORROWINGS ── --}}
        @if($section === 'borrowings')
        <div class="admin-page-header">
            <div class="admin-page-title">Borrowing Records</div>
            <div class="admin-page-sub">All active and historical borrow records.</div>
        </div>

        <div class="section-card">
            <table class="data-table">
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
                                    <form method="POST" action="{{ route('admin.borrowings.return', $borrowing->id) }}">
                                        @csrf
                                        <button type="submit" class="btn-sm">Return</button>
                                    </form>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty-state">No borrowing records yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── USERS & ROLES ── --}}
        @if($section === 'users')
        <div class="admin-page-header">
            <div class="admin-page-title">Users &amp; Role Management</div>
            <div class="admin-page-sub">Manage user accounts and assign roles.</div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">All Users</span>
                <span class="badge" style="font-size:11px;">{{ $users->count() }} total</span>
            </div>
            <div class="section-card-body" style="padding-bottom:16px;">
                <form method="GET" action="{{ route('admin.dashboard') }}" style="display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap;">
                    <input type="hidden" name="section" value="users">
                    <input type="text" name="user_search" value="{{ request('user_search') }}" placeholder="Search by ID, name, username, or email…" style="flex:1;min-width:200px;height:38px;font-size:13px;">
                    <button type="submit" class="btn-sm" style="height:38px;">Search</button>
                    @if(request('user_search'))
                        <a href="{{ route('admin.dashboard', ['section' => 'users']) }}" class="btn-sm btn-sm-outline" style="height:38px;display:inline-flex;align-items:center;text-decoration:none;">Clear</a>
                    @endif
                </form>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Current Role</th>
                        <th>Subscribed</th>
                        <th>Change Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
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
                            <td>
                                @php
                                    $roleClass = match($user->role) {
                                        'admin'  => 'role-admin',
                                        'staff'  => 'role-staff',
                                        default  => 'role-user',
                                    };
                                @endphp
                                <span class="badge {{ $roleClass }}">{{ ucfirst($user->role) }}</span>
                                @if($user->isSubscribed())
                                    <span class="badge role-subscribed" style="margin-top:3px;display:block;width:fit-content;">Subscribed</span>
                                @endif
                            </td>
                            <td>
                                @if($user->isSubscribed())
                                    <span class="badge badge-green">Active</span>
                                    <div class="cell-sub">Expires {{ $user->subscription_expires_at?->format('M d, Y') ?? 'N/A' }}</div>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.role', $user->id) }}" style="display:flex;gap:6px;align-items:center;">
                                        @csrf @method('PUT')
                                        <select name="role" style="padding:5px 8px;width:auto;font-size:12px;height:auto;">
                                            <option value="user"             {{ $user->role === 'user'            ? 'selected' : '' }}>User</option>
                                            <option value="subscribed_user"  {{ $user->role === 'subscribed_user' ? 'selected' : '' }}>Subscriber</option>
                                            <option value="staff"            {{ $user->role === 'staff'           ? 'selected' : '' }}>Staff</option>
                                            <option value="admin"            {{ $user->role === 'admin'           ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        <button type="submit" class="btn-sm">Save</button>
                                    </form>
                                @else
                                    <span class="muted" style="font-size:12px;">You</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">No users found.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── SUBMISSIONS ── --}}
        @if($section === 'submissions')
        <div class="admin-page-header">
            <div class="admin-page-title">Book Submissions</div>
            <div class="admin-page-sub">Review and approve or reject user-submitted books.</div>
        </div>

        <div class="section-card">
            <table class="data-table">
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
                                    <div class="table-actions">
                                        <form method="POST" action="{{ route('admin.submissions.approve', $sub->id) }}">
                                            @csrf
                                            <button type="submit" class="btn-sm btn-sm-green">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.submissions.reject', $sub->id) }}" onsubmit="return promptRejectReason(this)">
                                            @csrf
                                            <input type="hidden" name="rejection_reason" class="reject-reason-input">
                                            <button type="submit" class="btn-sm btn-sm-danger">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">No submissions yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── PAYMENTS ── --}}
        @if($section === 'payments')
        <div class="admin-page-header">
            <div class="admin-page-title">Payment Logs</div>
            <div class="admin-page-sub">All fine and subscription payment transactions.</div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">All Payments</span>
                <span class="badge" style="font-size:11px;">{{ $payments->count() }} total</span>
            </div>
            <div class="section-card-body" style="padding-bottom:16px;">
                <form method="GET" action="{{ route('admin.dashboard') }}" style="display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap;">
                    <input type="hidden" name="section" value="payments">
                    <input type="text" name="receipt_search" value="{{ request('receipt_search') }}" placeholder="Search by receipt ID, payment ID, or reference…" style="flex:1;min-width:200px;height:38px;font-size:13px;">
                    <button type="submit" class="btn-sm" style="height:38px;">Search</button>
                    @if(request('receipt_search'))
                        <a href="{{ route('admin.dashboard', ['section' => 'payments']) }}" class="btn-sm btn-sm-outline" style="height:38px;display:inline-flex;align-items:center;text-decoration:none;">Clear</a>
                    @endif
                </form>
            </div>
            <table class="data-table">
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
                        <tr><td colspan="7"><div class="empty-state">No payments yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- ── LOGS ── --}}
        @if($section === 'logs')
        <div class="admin-page-header">
            <div class="admin-page-title">Activity Logs</div>
            <div class="admin-page-sub">Recent system actions and events.</div>
        </div>

        <div class="section-card">
            <table class="data-table">
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
                        <tr><td colspan="4"><div class="empty-state">No activity logs yet.</div></td></tr>
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