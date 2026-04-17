@extends('layouts.app')

@section('title', 'Search')

@section('content')
    @php $currentUrl = request()->fullUrl(); @endphp

    <div class="card">
        <h1>Search</h1>
        <form method="GET" action="{{ route('search') }}" style="margin-top: 12px;">
            <input type="search" name="q" value="{{ $q }}" placeholder="Search books or users">
        </form>
    </div>

    <div class="card">
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

    <div class="card">
        <h2>Users</h2>
        @if($q === '')
            <p class="muted">User results will appear here too.</p>
        @elseif($users->isEmpty())
            <p class="muted">No users matched your search.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Bio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td><a href="{{ route('user.public_profile', $user->id) }}">{{ $user->name }}</a></td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>{{ $user->bio ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
