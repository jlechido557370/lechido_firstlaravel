<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:22px;">
    <h2 style="margin:0;font-size:22px;font-weight:600;color:var(--black);">Browse Books</h2>
    <a href="{{ route('books.catalogue') }}" style="font-size:14px;color:var(--muted);display:flex;align-items:center;gap:5px;transition:color .15s;" onmouseover="this.style.color='var(--black)'" onmouseout="this.style.color='var(--muted)'">
        Full Catalogue
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
    </a>
</div>
<p class="muted" id="browse-count" style="margin-bottom:18px;font-size:14px;">
    Showing {{ $books->count() }} book(s)
    @if(request('genre')) in <strong>{{ request('genre') }}</strong>@endif
    @if(request('search')) matching "<strong>{{ request('search') }}</strong>"@endif
</p>
<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:20px;">
    @forelse ($books as $book)
        <a href="{{ route('books.show', ['book' => $book->id, 'back' => request()->fullUrl()]) }}" class="book-card book-card-anim">
            <img src="{{ $book->coverUrl() }}"
                 alt="{{ $book->title }}"
                 loading="lazy"
                 onerror="this.onerror=null; this.src='{{ $book->noImageSvg() }}'">
            <div class="book-card-body">
                <div class="book-card-title">{{ $book->title }}</div>
                <div class="book-card-author">{{ $book->author }}</div>
                <div class="book-card-badges">
                    @if($book->genre)
                        <span class="badge" style="font-size:12px;">{{ $book->genre }}</span>
                    @endif
                    <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}" style="font-size:12px;">{{ ucfirst($book->status) }}</span>
                </div>
            </div>
        </a>
    @empty
        <p style="grid-column:1/-1;color:var(--muted);font-size:15px;">No books found.</p>
    @endforelse
</div>

