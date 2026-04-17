@extends('layouts.app')

@section('title', 'My Bookmarks')

@section('content')
    <div class="card">
        <h1>My Bookmarks</h1>
        <p class="muted">Books you have saved for later.</p>
    </div>

    @if($bookmarks->isEmpty())
        <div class="card">
            <p>You have no bookmarks yet. Visit a <a href="{{ route('home') }}">book page</a> and click Bookmark to save it here.</p>
        </div>
    @else
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Genre</th>
                        <th>Year</th>
                        <th>Status</th>
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
                                <a href="{{ route('books.show', $book->id) }}">{{ $book->title }}</a><br>
                                <span class="muted">{{ $book->author }}</span>
                            </td>
                            <td>{{ $book->genre }}</td>
                            <td>{{ $book->published_year }}</td>
                            <td>
                                <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}">
                                    {{ ucfirst($book->status) }}
                                </span>
                            </td>
                            <td class="muted">{{ $bm->created_at->format('M d, Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route('books.bookmark', $book->id) }}">
                                    @csrf
                                    <button type="submit" style="width: auto; padding: 6px 12px; background: #dc2626; border-color: #dc2626;">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection