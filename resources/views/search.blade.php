@extends('layouts.app')

@section('title', 'Search')

@section('content')
    @php $currentUrl = request()->fullUrl(); @endphp

    <div class="card reveal">
        <h1 style="margin-bottom:6px;">Search</h1>
        <p style="color:var(--muted);margin-bottom:18px;font-size:15px;">Find books by title, author, or genre — and discover other readers.</p>
        <form method="GET" action="{{ route('search') }}" style="margin-top: 12px;">
            <input type="search" name="q" value="{{ $q }}" placeholder="Search books or users…" style="font-size:16px;padding:13px 16px;">
        </form>
    </div>

    <div class="card reveal">
        <h2>Books</h2>
        @if($q === '')
            <p class="muted">Type something to search books and users.</p>
        @elseif($books->isEmpty())
            <p class="muted">No books matched your search.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Year</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                        <tr>
                            <td><a href="{{ route('books.show', ['book' => $book->id, 'back' => $currentUrl]) }}">{{ $book->title }}</a></td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $book->genre }}</td>
                            <td>{{ $book->published_year }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card reveal">
        <h2>Users</h2>
        @if($q === '')
            <p class="muted">User results will appear here too.</p>
        @elseif($users->isEmpty())
            <p class="muted">No users matched your search.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <a href="{{ route('user.public_profile', $user->id) }}">{{ $user->username ?? $user->displayName() }}</a>
                            </td>
                            <td>
                                @php
                                    $roleLabel = match($user->role) {
                                        'subscribed_user' => 'Subscriber',
                                        'admin' => 'Admin',
                                        'staff' => 'Staff',
                                        default => ucfirst($user->role),
                                    };
                                @endphp
                                <span class="badge">{{ $roleLabel }}</span>
                            </td>
                            <td>{{ $user->created_at?->format('M d, Y') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection