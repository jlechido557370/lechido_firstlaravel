@extends('layouts.app')

@section('title', $book->title)

@section('content')
    @php
        $backUrl    = request('back') ?: route('home');
        $currentUrl = request()->fullUrl();
        $isbnDisplay = $book->isbn_display;
    @endphp

    <style>
        .book-detail-hero {
            display: flex;
            gap: 28px;
            flex-wrap: wrap;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        .book-cover-wrap {
            flex-shrink: 0;
            position: relative;
        }
        .book-cover-wrap img {
            width: 160px;
            height: 235px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            background: var(--mid);
            transition: transform .25s ease;
        }
        .book-cover-wrap:hover img { transform: scale(1.02); }
        .book-detail-meta { flex: 1; min-width: 260px; }
        .book-detail-title {
            font-size: 26px;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 6px;
            line-height: 1.3;
        }
        .book-detail-author {
            font-size: 16px;
            color: var(--muted);
            margin-bottom: 14px;
        }
        .book-detail-author strong { color: var(--black); font-weight: 500; }
        .book-detail-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 18px; }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            margin: 16px 0;
        }
        .info-cell {
            padding: 12px 16px;
            border-right: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            background: var(--off);
        }
        .info-cell:nth-child(even) { border-right: none; }
        .info-cell:nth-last-child(-n+2) { border-bottom: none; }
        @media (max-width: 500px) {
            .info-grid { grid-template-columns: 1fr; }
            .info-cell { border-right: none !important; }
        }
        .info-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            font-family: var(--font-mono);
            color: var(--muted);
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 14.5px;
            color: var(--black);
            font-weight: 500;
        }
        .info-value code {
            font-family: var(--font-mono);
            font-size: 13px;
            background: var(--mid);
            padding: 2px 6px;
            border-radius: 4px;
        }

        .action-row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 14px;
        }
        .action-row form { margin: 0; }
        .action-row .btn-read {
            background: #15803d !important;
            border-color: #15803d !important;
            color: #fff !important;
        }
        .action-row .btn-read:hover {
            background: #16a34a !important;
            border-color: #16a34a !important;
        }
        .action-row .btn-return {
            background: transparent !important;
            color: var(--black) !important;
            border-color: var(--border) !important;
        }
        .action-row .btn-return:hover {
            border-color: var(--black) !important;
            background: var(--off) !important;
        }

        .review-item {
            display: flex;
            gap: 14px;
            padding: 16px 0;
            border-bottom: 1px solid var(--mid);
        }
        .review-item:last-child { border-bottom: none; }
        .review-avatar {
            width: 40px; height: 40px;
            object-fit: cover; border-radius: 50%;
            border: 1.5px solid var(--border);
            flex-shrink: 0;
        }
        .review-header {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 4px; flex-wrap: wrap;
        }
        .review-stars { font-size: 15px; letter-spacing: 1px; }
        .review-body {
            font-size: 14.5px;
            color: var(--black);
            line-height: 1.65;
            margin: 0;
        }
        .review-date {
            font-size: 11px;
            color: var(--muted);
            font-family: var(--font-mono);
        }

        .star-picker { display: flex; gap: 6px; }
        .star-pick {
            font-size: 30px; cursor: pointer;
            transition: color .12s, transform .12s;
            user-select: none;
        }
        .star-pick:hover { transform: scale(1.1); }

        .description-text {
            font-size: 15.5px;
            line-height: 1.8;
            color: var(--black);
            max-width: 680px;
        }
        .limit-notice {
            font-size: 13px;
            color: var(--muted);
            margin-top: 10px;
        }
        .limit-notice a { font-weight: 500; }
    </style>

    {{-- Breadcrumbs --}}
    <div class="card" style="padding:18px 24px; margin-bottom:16px;">
        <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted); flex-wrap:wrap;">
            <a href="{{ $backUrl }}" style="display:inline-flex; align-items:center; gap:4px; color:var(--muted);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                Back
            </a>
            <span style="color:var(--border);">/</span>
            <a href="{{ route('home') }}" style="color:var(--muted);">Home</a>
            <span style="color:var(--border);">/</span>
            <a href="{{ route('books.catalogue') }}" style="color:var(--muted);">Catalogue</a>
            <span style="color:var(--border);">/</span>
            <span style="color:var(--black); font-weight:500;">{{ Str::limit($book->title, 40) }}</span>
        </div>
    </div>

    {{-- Main Book Detail --}}
    <div class="card" style="padding:32px;">
        <div class="book-detail-hero">
            <div class="book-cover-wrap">
                <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='{{ $book->noImageSvg() }}'">
            </div>

            <div class="book-detail-meta">
                <h1 class="book-detail-title">{{ $book->title }}</h1>
                <p class="book-detail-author">by <strong>{{ $book->author }}</strong></p>

                <div class="book-detail-badges">
                    @if($book->genre)
                        <span class="badge">{{ $book->genre }}</span>
                    @endif
                    <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}">
                        {{ ucfirst($book->status) }}
                    </span>
                    @if($avgRating)
                        <span class="badge" style="background: rgba(245,158,11,.12); color: #b45309;">
                            ★ {{ $avgRating }}
                            <span class="muted" style="font-size: 11px; margin-left:3px;">({{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }})</span>
                        </span>
                    @endif
                </div>

                <div class="info-grid">
                    @if($isbnDisplay['isbn_13'])
                    <div class="info-cell">
                        <div class="info-label">ISBN-13</div>
                        <div class="info-value"><code>{{ $isbnDisplay['isbn_13'] }}</code></div>
                    </div>
                    @endif
                    @if($isbnDisplay['isbn_10'])
                    <div class="info-cell">
                        <div class="info-label">ISBN-10</div>
                        <div class="info-value"><code>{{ $isbnDisplay['isbn_10'] }}</code></div>
                    </div>
                    @endif
                    <div class="info-cell">
                        <div class="info-label">Published</div>
                        <div class="info-value">{{ $book->published_year }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Copies Available</div>
                        <div class="info-value">
                            <strong style="color: {{ $book->available_copies > 0 ? '#16a34a' : '#dc2626' }}">{{ $book->available_copies }}</strong>
                            <span class="muted"> / {{ $book->total_copies }}</span>
                        </div>
                    </div>
                </div>

                @if($book->description)
                    <p class="description-text">{{ $book->description }}</p>
                @endif

                @auth
                    @if(auth()->user()->isPatron())
                        @php
                            $activeBorrowCount = \App\Models\BorrowRecord::where('user_id', auth()->id())->whereNull('returned_at')->count();
                            $userLimit = auth()->user()->isSubscribed() ? 25 : 5;
                        @endphp

                        <div class="action-row">
                            {{-- Bookmark --}}
                            <form method="POST" action="{{ route('books.bookmark', $book->id) }}" style="display:inline;"
                                  onsubmit="event.preventDefault(); ajaxFormSubmit(this, {loadingClass:'btn-loading', onSuccess: function(){ var btn = document.getElementById('bookmarkBtn'); if(btn) { var t = btn.textContent.trim(); btn.textContent = t === 'Bookmark' ? 'Bookmarked' : 'Bookmark'; btn.classList.toggle('btn-outline'); } }});">
                                @csrf
                                <button type="submit" id="bookmarkBtn" class="{{ $isBookmarked ? 'btn-outline' : '' }}" style="padding:9px 18px; font-size:13.5px;">
                                    {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                                </button>
                            </form>

                            {{-- Read --}}
                            @if($canRead)
                                <a href="{{ route('books.read', ['book' => $book->id, 'back' => $currentUrl]) }}"
                                   id="readBookBtn"
                                   class="btn-read"
                                   style="padding:9px 18px; border-radius:9px; font-size:13.5px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; font-weight:500;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                    Read Book Online
                                </a>
                            @endif

                            {{-- Borrow / Reserve / Return --}}
                            @if($alreadyBorrowed && $activeBorrowing)
                                <form method="POST" action="{{ route('user.borrowings.return', $activeBorrowing->id) }}" style="margin:0;"
                                      onsubmit="event.preventDefault(); ajaxFormSubmit(this, {loadingClass:'btn-loading', onSuccess: function(){ location.reload(); }});">
                                    @csrf
                                    <button type="submit" class="btn-return" style="padding:9px 18px; font-size:13.5px;">Return Book</button>
                                </form>
                            @elseif($book->available_copies > 0 && !$atLimit)
                                <form method="POST" action="{{ route('books.borrow', $book->id) }}" style="margin:0;"
                                      onsubmit="event.preventDefault(); handleBorrow(this);">
                                    @csrf
                                    <button type="submit" style="padding:9px 18px; font-size:13.5px; display:inline-flex; align-items:center; gap:6px;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                        Borrow this Book
                                    </button>
                                </form>
                            @elseif($atLimit)
                                <span class="badge" style="padding:8px 14px; font-size:13px;">Borrowing limit reached</span>
                            @elseif($book->available_copies <= 0 && !$alreadyReserved)
                                <form method="POST" action="{{ route('books.reserve', $book->id) }}" style="margin:0;"
                                      onsubmit="event.preventDefault(); ajaxFormSubmit(this, {loadingClass:'btn-loading'});">
                                    @csrf
                                    <button type="submit" class="btn-outline" style="padding:9px 18px; font-size:13.5px;">Reserve this Book</button>
                                </form>
                            @elseif($alreadyReserved)
                                <span class="badge" style="padding:8px 14px; font-size:13px;">Already reserved</span>
                            @endif
                        </div>

                        <p class="limit-notice">
                            You have <strong>{{ $activeBorrowCount }} / {{ $userLimit }}</strong> books borrowed.
                            @if(!auth()->user()->isSubscribed())
                                <a href="{{ route('subscription.index') }}">Subscribe</a> to borrow up to 25.
                            @endif
                        </p>
                    @endif
                @else
                    <p class="muted" style="margin-top:12px;">
                        <a href="{{ route('login') }}">Login</a> or <a href="{{ route('register') }}">Register</a> to borrow, bookmark, or rate this book.
                    </p>
                @endauth
            </div>
        </div>
    </div>

    {{-- REVIEWS & RATING --}}
    @auth
        @if(auth()->user()->isPatron())
        <div class="card" style="padding:28px;">
            <h2 style="font-size:18px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                Ratings &amp; Reviews
            </h2>

            {{-- Existing reviews --}}
            @if($reviews->isEmpty())
                <p class="muted" style="margin-bottom:24px;">No reviews yet. Be the first to share your thoughts!</p>
            @else
                <div style="margin-bottom:28px;">
                    @foreach($reviews as $review)
                        <div class="review-item">
                            <img src="{{ $review->user?->avatarUrl() }}" alt="" class="review-avatar">
                            <div style="flex:1; min-width:0;">
                                <div class="review-header">
                                    <strong style="font-size:14px; color:var(--black);">{{ $review->user?->name ?? 'User' }}</strong>
                                    <span class="review-stars">
                                        @for($i=1;$i<=5;$i++)
                                            <span style="color:{{ $review->rating >= $i ? '#f59e0b' : 'var(--mid)' }};">★</span>
                                        @endfor
                                    </span>
                                    <span class="review-date">{{ $review->created_at->format('M d, Y') }}</span>
                                </div>
                                @if($review->comment)
                                    <p class="review-body">{{ $review->comment }}</p>
                                @endif
                                @if(auth()->id() === $review->user_id)
                                    <form method="POST" action="{{ route('books.reviews.destroy', $book->id) }}" style="margin-top:8px; display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-outline" style="font-size:11px; padding:3px 10px; color:var(--muted); border-color:var(--border);" onclick="return confirm('Remove your review?')">Remove</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Rate / update form --}}
            @php $myReview = $reviews->firstWhere('user_id', auth()->id()); @endphp
            <div style="border-top:1px solid var(--border); padding-top:24px;">
                <h3 style="font-size:15px; font-weight:600; margin-bottom:16px; color:var(--black);">
                    {{ $myReview ? 'Update Your Review' : 'Leave a Review' }}
                </h3>
                <form method="POST" action="{{ route('books.reviews.store', $book->id) }}">
                    @csrf
                    {{-- Star picker --}}
                    <div style="margin-bottom:16px;">
                        <label style="font-size:13px; font-weight:600; margin-bottom:8px; display:block;">Rating <span style="color:#dc2626;">*</span></label>
                        <div class="star-picker" id="starPicker">
                            @for($s=1;$s<=5;$s++)
                                <span class="star-pick" data-val="{{ $s }}" onclick="pickStar({{ $s }})"
                                    style="color:{{ ($myReview && $myReview->rating >= $s) ? '#f59e0b' : 'var(--mid)' }};">★</span>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="{{ $myReview->rating ?? '' }}" required>
                        @error('rating')<div style="color:#dc2626; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Comment <span class="muted" style="font-weight:400;">(optional)</span></label>
                        <textarea name="comment" rows="3" placeholder="Share your thoughts about this book…" style="resize:vertical; font-size:15px; padding:11px 14px;">{{ old('comment', $myReview->comment ?? '') }}</textarea>
                        @error('comment')<div style="color:#dc2626; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" style="padding:10px 24px; font-size:14px;">
                        {{ $myReview ? 'Update Review' : 'Submit Review' }}
                    </button>
                </form>
            </div>
        </div>
        @endif
    @endauth
@endsection

@push('scripts')
<script>
var _pickedRating = parseInt(document.getElementById('ratingInput')?.value) || 0;

function pickStar(val) {
    _pickedRating = val;
    document.getElementById('ratingInput').value = val;
    updateStars(val);
}

function updateStars(val) {
    document.querySelectorAll('.star-pick').forEach(function(s) {
        s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : 'var(--mid)';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var stars = document.querySelectorAll('.star-pick');
    stars.forEach(function(s) {
        s.addEventListener('mouseenter', function() { updateStars(parseInt(s.dataset.val)); });
        s.addEventListener('mouseleave', function() { updateStars(_pickedRating); });
    });
    if (_pickedRating) updateStars(_pickedRating);
});
</script>
@endpush

