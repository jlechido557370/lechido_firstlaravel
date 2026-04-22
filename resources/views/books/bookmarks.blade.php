@extends('layouts.app')

@section('title', 'My Bookmarks')

@section('content')
    <div class="card">
        <h1>My Bookmarks</h1>
        <p class="muted">Books you have saved.</p>
    </div>

    <div class="card">
        <h2>Bookmarks</h2>

        @if($bookmarks->isEmpty())
            <p class="muted">
                You have no bookmarked books. Visit a book page and click "Bookmark".
            </p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Saved On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookmarks as $bm)
                        @php $book = $bm->book; @endphp

                        @if($book)
                        <tr>
                            <td>
                                <a href="{{ route('books.show', $book) }}">
                                    {{ $book->title }}
                                </a>
                            </td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $book->genre }}</td>
                            <td>{{ $bm->created_at->format('M d, Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route('books.bookmark', $book) }}">
                                    @csrf
                                    <button type="submit"
                                            style="width:auto; padding:4px 10px; background:#dc2626;">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endif

                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection