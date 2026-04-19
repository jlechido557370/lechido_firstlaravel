@extends('layouts.app')

@section('title', 'My Bookmarks & Lists')

@section('content')
    <div class="card">
        <h1>My Bookmarks &amp; Lists</h1>
        <p class="muted">Books you have saved (bookmarks) and comic/manga series you have listed.</p>
    </div>

    <div class="card">
        <h2>Bookmarks (Books)</h2>
        @if($bookmarks->isEmpty())
            <p class="muted">You have no bookmarked books. Visit a book page and click "Bookmark".</p>
        @else
            <table>
                <thead>
                    <tr><th>Book</th><th>Author</th><th>Genre</th><th>Saved On</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @foreach($bookmarks as $bm)
                        @php $book = $bm->book; @endphp
                        @if($book)
                        <tr>
                            <td><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $book->genre }}</td>
                            <td>{{ $bm->created_at->format('M d, Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route('books.bookmark', $book) }}">
                                    @csrf
                                    <button type="submit" style="width: auto; padding: 4px 10px; background: #dc2626;">Remove</button>
                                </form>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card">
        <h2>Lists (Comics/Manga)</h2>
        @if($lists->isEmpty())
            <p class="muted">You have no saved series. Visit a comic or manga series page and click "List".</p>
        @else
            <table>
                <thead>
                    <tr><th>Series</th><th>Type</th><th>Saved On</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @foreach($lists as $list)
                        <tr>
                            <td><a href="{{ route('series.show', $list->series) }}">{{ $list->series->title }}</a></td>
                            <td><span class="badge">{{ ucfirst($list->series->book_type) }}</span></td>
                            <td>{{ $list->created_at->format('M d, Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route('series.toggleList', $list->series) }}">
                                    @csrf
                                    <button type="submit" style="width: auto; padding: 4px 10px; background: #dc2626;">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection