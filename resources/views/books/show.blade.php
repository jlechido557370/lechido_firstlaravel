@extends('layouts.app')

@section('title', $book->title)

@section('content')
    @php
        $backUrl    = request('back') ?: route('home');
        $currentUrl = request()->fullUrl();
        $isbnDisplay = $book->isbn_display;
    @endphp

    <div class="card">
        <p style="margin-bottom: 8px;">
            <a href="{{ $backUrl }}">&larr; Back</a>
            &nbsp;&bull;&nbsp;
            <a href="{{ route('home') }}">Home</a>
            &nbsp;&bull;&nbsp;
            <a href="{{ route('books.catalogue') }}">Catalogue</a>
        </p>

        <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-start; margin-bottom: 16px;">
            <img src="{{ $book->coverUrl() }}"
                 alt="{{ $book->title }}"
                 loading="lazy"
                 style="width: 140px; height: 200px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb; flex-shrink: 0; background: #e5e7eb;"
                 onerror="this.onerror=null; this.src='{{ $book->noImageSvg() }}'">

            <div style="flex: 1; min-width: 200px;">
                <h1 style="margin-bottom: 4px;">{{ $book->title }}</h1>
                <p class="muted" style="font-size: 16px; margin-bottom: 12px;">
                    by <strong>{{ $book->author }}</strong>
                </p>

                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                    @if($book->genre)
                        <span class="badge">{{ $book->genre }}</span>
                    @endif

                    <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}">
                        {{ ucfirst($book->status) }}
                    </span>

                    @if($avgRating)
                        <span class="badge" style="background: #fef9c3;">
                            &#9733; {{ $avgRating }} / 5
                            <span class="muted" style="font-size: 11px;">
                                ({{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }})
                            </span>
                        </span>
                    @endif
                </div>

                <table style="width: auto; margin: 16px 0;">
                    <tr>
                        <td style="padding: 4px 20px 4px 0; color: #6b7280;">Published</td>
                        <td>{{ $book->published_year }}</td>
                    </tr>

                    @if($isbnDisplay['isbn_13'])
                    <tr>
                        <td style="color: #6b7280;">ISBN-13</td>
                        <td style="font-family: monospace;">{{ $isbnDisplay['isbn_13'] }}</td>
                    </tr>
                    @endif

                    @if($isbnDisplay['isbn_10'])
                    <tr>
                        <td style="color: #6b7280;">ISBN-10</td>
                        <td style="font-family: monospace;">{{ $isbnDisplay['isbn_10'] }}</td>
                    </tr>
                    @endif

                    <tr>
                        <td style="color: #6b7280;">Copies Available</td>
                        <td>{{ $book->available_copies }} / {{ $book->total_copies }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($book->description)
            <p style="line-height: 1.7; margin-bottom: 20px;">
                {{ $book->description }}
            </p>
        @endif

        @auth
            @if(auth()->user()->role === 'user')

                {{-- Bookmark --}}
                <form method="POST" action="{{ route('books.bookmark', $book->id) }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="padding:8px 16px;">
                        {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                    </button>
                </form>

                {{-- Read --}}
                @if($canRead)
                    <a href="{{ route('books.read', ['book' => $book->id, 'back' => $currentUrl]) }}"
                       style="padding:8px 16px; background:#15803d; color:white; border-radius:6px; text-decoration:none;">
                        Read Book Online
                    </a>
                @endif

                {{-- Borrow / Reserve --}}
                <div style="margin-top:12px;">
                    @if($alreadyBorrowed)
                        <span class="badge">You currently have this book borrowed</span>

                    @elseif($book->available_copies > 0 && !$atLimit)
                        <form method="POST" action="{{ route('books.borrow', $book->id) }}">
                            @csrf
                            <button type="submit">Borrow this Book</button>
                        </form>

                    @elseif($atLimit)
                        <p class="muted">Borrowing limit reached.</p>

                    @elseif($book->available_copies <= 0 && !$alreadyReserved)
                        <form method="POST" action="{{ route('books.reserve', $book->id) }}">
                            @csrf
                            <button type="submit">Reserve this Book</button>
                        </form>

                    @elseif($alreadyReserved)
                        <span class="badge">Already reserved</span>
                    @endif
                </div>

            @endif
        @else
            <p class="muted">
                <a href="{{ route('login') }}">Login</a> or
                <a href="{{ route('register') }}">Register</a>
            </p>
        @endauth
    </div>

    {{-- REVIEWS & RATING --}}
    @auth
        @if(auth()->user()->role === 'user')
        <div class="card">
            <h2>Ratings &amp; Reviews</h2>

            {{-- Existing reviews --}}
            @if($reviews->isEmpty())
                <p class="muted" style="margin-bottom:20px;">No reviews yet. Be the first!</p>
            @else
                <div style="margin-bottom:24px;">
                    @foreach($reviews as $review)
                        <div style="display:flex;gap:14px;padding:14px 0;border-bottom:1px solid var(--mid);">
                            <img src="{{ $review->user?->avatarUrl() }}" alt="" style="width:36px;height:36px;object-fit:cover;border-radius:50%;border:1px solid var(--border);flex-shrink:0;">
                            <div style="flex:1;">
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;flex-wrap:wrap;">
                                    <strong style="font-size:14px;color:var(--black);">{{ $review->user?->name ?? 'User' }}</strong>
                                    <span style="font-size:15px;letter-spacing:1px;">
                                        @for($i=1;$i<=5;$i++)
                                            <span style="color:{{ $review->rating >= $i ? '#f59e0b' : 'var(--mid)' }};">★</span>
                                        @endfor
                                    </span>
                                    <span style="font-size:11px;color:var(--muted);font-family:var(--font-mono);">{{ $review->created_at->format('M d, Y') }}</span>
                                </div>
                                @if($review->comment)
                                    <p style="font-size:14px;color:var(--black);line-height:1.6;margin:0;">{{ $review->comment }}</p>
                                @endif
                                @if(auth()->id() === $review->user_id)
                                    <form method="POST" action="{{ route('books.reviews.destroy', $book->id) }}" style="margin-top:6px;display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-outline" style="font-size:11px;padding:3px 10px;color:var(--muted);border-color:var(--border);" onclick="return confirm('Remove your review?')">Remove</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Rate / update form --}}
            @php $myReview = $reviews->firstWhere('user_id', auth()->id()); @endphp
            <div style="border-top:1px solid var(--border);padding-top:20px;">
                <h3 style="font-size:15px;font-weight:600;margin-bottom:14px;color:var(--black);">
                    {{ $myReview ? 'Update Your Review' : 'Leave a Review' }}
                </h3>
                <form method="POST" action="{{ route('books.reviews.store', $book->id) }}">
                    @csrf
                    {{-- Star picker --}}
                    <div style="margin-bottom:14px;">
                        <label style="font-size:13px;margin-bottom:8px;display:block;">Rating *</label>
                        <div class="star-picker" id="starPicker">
                            @for($s=1;$s<=5;$s++)
                                <span class="star-pick" data-val="{{ $s }}" onclick="pickStar({{ $s }})"
                                    style="font-size:28px;cursor:pointer;color:{{ ($myReview && $myReview->rating >= $s) ? '#f59e0b' : 'var(--mid)' }};transition:color .12s;user-select:none;">★</span>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="{{ $myReview->rating ?? '' }}" required>
                        @error('rating')<div style="color:#b91c1c;font-size:13px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:13px;margin-bottom:6px;display:block;">Comment <span class="muted" style="font-weight:400;">(optional)</span></label>
                        <textarea name="comment" rows="3" placeholder="Share your thoughts about this book…" style="resize:vertical;">{{ old('comment', $myReview->comment ?? '') }}</textarea>
                        @error('comment')<div style="color:#b91c1c;font-size:13px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" style="padding:9px 22px;">{{ $myReview ? 'Update Review' : 'Submit Review' }}</button>
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