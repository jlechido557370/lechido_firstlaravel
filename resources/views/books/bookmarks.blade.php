@extends('layouts.app')

@section('title', 'My Bookmarks')

@section('content')
    <div class="section-header">
        <div>
            <h1>My Bookmarks</h1>
            <p class="muted">Books you have saved for later.</p>
        </div>
        <a href="{{ route('books.catalogue') }}" class="section-header-link">
            Browse Catalogue &rarr;
        </a>
    </div>

    @if($bookmarks->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">🔖</div>
                <div class="empty-state-title">No bookmarks yet</div>
                <div class="empty-state-desc">
                    Visit a book page and click "Bookmark" to save it here for quick access.
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-4">
            @foreach($bookmarks as $bm)
                @php $book = $bm->book; @endphp
                @if($book)
                <div class="book-card" onclick="window.location='{{ route('books.show', $book) }}'">
                    <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" loading="lazy"
                         onerror="this.onerror=null; this.src='{{ $book->noImageSvg() }}'">
                    <div class="book-card-body">
                        <div class="book-card-title">{{ $book->title }}</div>
                        <div class="book-card-author">{{ $book->author }}</div>
                        <div class="book-card-badges">
                            @if($book->genre)
                                <span class="badge">{{ $book->genre }}</span>
                            @endif
                            <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}">
                                {{ ucfirst($book->status) }}
                            </span>
                        </div>
                        <div style="margin-top:10px; display:flex; align-items:center; justify-content:space-between;">
                            <span style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">
                                Saved {{ $bm->created_at->format('M d, Y') }}
                            </span>
                            <form method="POST" action="{{ route('books.bookmark', $book) }}" style="margin:0;"
                                  onclick="event.stopPropagation();">
                                @csrf
                                <button type="submit" class="btn-outline" style="padding:4px 10px; font-size:12px;">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    @endif
@endsection
