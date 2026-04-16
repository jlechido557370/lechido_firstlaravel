@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="card">
        <h1>Admin Dashboard</h1>
        <p class="muted">Manage books, borrowings, users, reservations, and activity logs.</p>
        <div class="row">
            <a href="{{ route('admin.dashboard', ['section' => 'overview']) }}">Overview</a>
            <a href="{{ route('admin.dashboard', ['section' => 'books']) }}">Books</a>
            <a href="{{ route('admin.dashboard', ['section' => 'borrowings']) }}">Borrowings</a>
            <a href="{{ route('admin.dashboard', ['section' => 'reservations']) }}">Reservations</a>
            <a href="{{ route('admin.dashboard', ['section' => 'users']) }}">Users</a>
            <a href="{{ route('admin.dashboard', ['section' => 'logs']) }}">Logs</a>
        </div>
    </div>

    @if($section === 'overview')
        <div class="grid grid-4">
            <div class="card"><div class="muted">Total Books</div><div class="stats">{{ $stats['total_books'] }}</div></div>
            <div class="card"><div class="muted">Available Copies</div><div class="stats">{{ $stats['available_copies'] }}</div></div>
            <div class="card"><div class="muted">Active Borrows</div><div class="stats">{{ $stats['active_borrows'] }}</div></div>
            <div class="card"><div class="muted">Total Users</div><div class="stats">{{ $stats['total_users'] }}</div></div>
        </div>
    @endif

    @if($section === 'books')
        <div class="card">
            <h2>{{ $editingBook ? 'Edit Book' : 'Add Book' }}</h2>
            <form method="POST" action="{{ $editingBook ? route('admin.books.update', $editingBook->id) : route('admin.books.store') }}">
                @csrf
                @if($editingBook) @method('PUT') @endif

                <div class="row">
                    <div><label>Title</label><input type="text" name="title" value="{{ old('title', $editingBook->title ?? '') }}" required></div>
                    <div><label>Author</label><input type="text" name="author" value="{{ old('author', $editingBook->author ?? '') }}" required></div>
                </div>

                <div class="row">
                    <div><label>ISBN</label><input type="text" name="isbn" value="{{ old('isbn', $editingBook->isbn ?? '') }}" required></div>
                    <div><label>Genre</label><input type="text" name="genre" value="{{ old('genre', $editingBook->genre ?? '') }}" required></div>
                </div>

                <div class="row">
                    <div><label>Published Year</label><input type="number" name="published_year" value="{{ old('published_year', $editingBook->published_year ?? '') }}" required></div>
                    <div><label>Total Copies</label><input type="number" name="total_copies" min="1" value="{{ old('total_copies', $editingBook->total_copies ?? 1) }}" required></div>
                </div>

                <div style="margin-bottom:12px;">
                    <label>Description</label>
                    <textarea name="description" rows="3">{{ old('description', $editingBook->description ?? '') }}</textarea>
                </div>

                <div style="margin-bottom:12px;">
                    <label style="display:block;margin-bottom:4px;font-weight:600;font-size:13px;">
                        Read URL <span class="muted" style="font-weight:400;">(optional — link to online version or PDF)</span>
                    </label>
                    <input type="url" name="read_url" placeholder="https://www.gutenberg.org/..." value="{{ old('read_url', $editingBook->read_url ?? '') }}">
                </div>

                <button type="submit">{{ $editingBook ? 'Update Book' : 'Save Book' }}</button>
            </form>

            @if($editingBook)
                <p><a href="{{ route('admin.dashboard', ['section' => 'books']) }}">Cancel edit</a></p>
            @endif
        </div>

        <div class="card">
            <h2>Book List</h2>
            <table>
                <thead>
                    <tr><th>Title</th><th>Copies</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>{{ $book->title }}<br><span class="muted">{{ $book->author }}</span></td>
                            <td>{{ $book->available_copies }} / {{ $book->total_copies }}</td>
                            <td>{{ ucfirst($book->status) }}</td>
                            <td>
                                <a href="{{ route('admin.dashboard', ['section' => 'books', 'edit' => $book->id]) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.books.destroy', $book->id) }}" style="display:inline;" onsubmit="return confirm('Delete this book?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="width:auto; padding:6px 10px;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No books yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
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
                                @if(! $borrowing->returned_at)
                                    <form method="POST" action="{{ route('admin.borrowings.return', $borrowing->id) }}">
                                        @csrf
                                        <button type="submit" style="width:auto; padding:6px 10px;">Return</button>
                                    </form>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No borrowing records yet.</td></tr>
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
                                    <form method="POST" action="{{ route('admin.reservations.fulfill', $res->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" style="width:auto; padding:6px 10px;">Fulfill</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reservations.cancel', $res->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" style="width:auto; padding:6px 10px;">Cancel</button>
                                    </form>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No reservations yet.</td></tr>
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
                    <tr><th>Name</th><th>Email</th><th>Role</th></tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role ?? 'user') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No users found.</td></tr>
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