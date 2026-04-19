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
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <h1 style="margin-bottom: 4px;">{{ $book->title }}</h1>
                        <p class="muted" style="font-size: 16px; margin-bottom: 12px;">by <strong>{{ $book->author }}</strong></p>
                        <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                            @if($book->genre)
                                <span class="badge">{{ $book->genre }}</span>
                            @endif
                            @if($book->showTypeBadge())
                                <span class="badge" style="background: #e0e7ff;">{{ ucfirst($book->book_type) }}</span>
                            @endif
                            <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}">
                                {{ ucfirst($book->status) }}
                            </span>
                            @if($avgRating)
                                <span class="badge" style="background: #fef9c3;">
                                    &#9733; {{ $avgRating }} / 5
                                    <span class="muted" style="font-size: 11px;">({{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }})</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        @auth
                            @if(auth()->user()->role === 'user')
                                {{-- Bookmark button only for books --}}
                                @if($book->book_type === 'book')
                                    <form method="POST" action="{{ route('books.bookmark', $book->id) }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" style="width: auto; padding: 8px 16px; background: {{ $isBookmarked ? '#374151' : '#111827' }};">
                                            {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endauth

                        {{-- Read button only for books that are borrowed --}}
                        @if($canRead && $book->book_type === 'book')
                            <a href="{{ route('books.read', ['book' => $book->id, 'back' => $currentUrl]) }}"
                               style="display: inline-block; padding: 8px 16px; background: #15803d; color: white; border-radius: 6px; text-decoration: none; font-size: 14px;">
                                Read Book Online
                            </a>
                        @endif
                    </div>
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
                        <td style="color: #6b7280;">Type</td>
                        <td>{{ ucfirst($book->book_type ?? 'book') }}</td>
                    </tr>
                    <tr>
                        <td style="color: #6b7280;">Copies Available</td>
                        <td>{{ $book->available_copies }} / {{ $book->total_copies }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($book->description)
            <p style="line-height: 1.7; margin-bottom: 20px;">{{ $book->description }}</p>
        @endif

        @auth
            @if(auth()->user()->role === 'user')
                {{-- Borrow/Reserve logic only for books --}}
                @if($book->book_type === 'book')
                    @if($alreadyBorrowed)
                        <p><span class="badge" style="font-size: 14px; padding: 8px 14px;">You currently have this book borrowed</span></p>
                    @elseif($book->available_copies > 0 && !$atLimit)
                        <form method="POST" action="{{ route('books.borrow', $book->id) }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="width: auto; padding: 10px 24px;">Borrow this Book</button>
                        </form>
                    @elseif($atLimit && $book->available_copies > 0)
                        @if(!auth()->user()->isSubscribed())
                            <p class="muted">You have reached the 5-book free limit. <a href="{{ route('subscription.index') }}">Subscribe</a> to borrow up to 25 books.</p>
                        @else
                            <p class="muted">You have reached the 25-book limit. Return a book first.</p>
                        @endif
                    @elseif($book->available_copies <= 0 && !$alreadyReserved)
                        <form method="POST" action="{{ route('books.reserve', $book->id) }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="width: auto; padding: 10px 24px;">Reserve this Book</button>
                        </form>
                        <p class="muted" style="margin-top: 8px; font-size: 13px;">No copies available — reserve to be notified when one is.</p>
                    @elseif($alreadyReserved)
                        <p><span class="badge" style="font-size: 14px; padding: 8px 14px;">You have already reserved this book</span></p>
                    @endif
                @else
                    <p class="muted">Comics and Manga cannot be borrowed. You can only read them online.</p>
                @endif
            @endif
        @else
            <p class="muted">
                <a href="{{ route('login') }}">Login</a> or <a href="{{ route('register') }}">Register</a> to borrow or reserve this book.
            </p>
        @endauth
    </div>

    {{-- RATINGS & REVIEWS SECTION (shown for all book types) --}}
    @auth
        <div class="card">
            <h2 style="margin-bottom: 16px;">Ratings &amp; Reviews</h2>

            @if(auth()->user()->role === 'user')
                <div style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="margin-bottom: 10px; font-size: 15px;">{{ $userReview ? 'Edit Your Review' : 'Leave a Review' }}</h3>
                    <form method="POST" action="{{ route('books.reviews.store', $book->id) }}">
                        @csrf
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Your Rating</label>
                            <div id="star-rating" style="display: flex; gap: 6px;">
                                @for($i = 1; $i <= 5; $i++)
                                    <label style="cursor: pointer; font-size: 28px; color: {{ ($userReview && $userReview->rating >= $i) ? '#f59e0b' : '#d1d5db' }};"
                                           id="star-label-{{ $i }}"
                                           onmouseover="hoverStars({{ $i }})"
                                           onmouseout="resetStars()">
                                        <input type="radio" name="rating" value="{{ $i }}"
                                               {{ ($userReview && $userReview->rating == $i) ? 'checked' : '' }}
                                               style="display: none;"
                                               onchange="selectStar({{ $i }})">
                                        &#9733;
                                    </label>
                                @endfor
                            </div>
                            @error('rating') <p style="color: #dc2626; font-size: 13px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Comment <span class="muted" style="font-weight: 400;">(optional)</span></label>
                            <textarea name="comment" rows="3" placeholder="Share your thoughts about this book..." style="resize: vertical;">{{ old('comment', $userReview?->comment) }}</textarea>
                            @error('comment') <p style="color: #dc2626; font-size: 13px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" style="width: auto; padding: 8px 20px;">
                            {{ $userReview ? 'Update Review' : 'Submit Review' }}
                        </button>
                    </form>
                    @if($userReview)
                        <form method="POST" action="{{ route('books.reviews.destroy', $book->id) }}" style="margin-top: 8px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="width: auto; padding: 8px 20px; background: #dc2626;" onclick="return confirm('Remove your review?')">
                                Remove Review
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            @if($reviews->isEmpty())
                <p class="muted">No reviews yet. Be the first to leave one!</p>
            @else
                @foreach($reviews as $review)
                    <div style="padding: 14px 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; gap: 12px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <a href="{{ route('user.public_profile', $review->user_id) }}" style="font-size: 14px; font-weight: 700;">{{ $review->user?->publicDisplayName() ?? 'Unknown' }}</a>
                                <span style="font-size: 18px; letter-spacing: -1px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span style="color: {{ $review->rating >= $i ? '#f59e0b' : '#d1d5db' }};">&#9733;</span>
                                    @endfor
                                </span>
                                <span class="muted" style="font-size: 12px;">{{ $review->rating }}/5</span>
                            </div>
                            <span class="muted" style="font-size: 12px;">{{ $review->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($review->comment)
                            <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #374151;">{{ $review->comment }}</p>
                        @else
                            <p class="muted" style="font-size: 13px; margin: 0;">No comment left.</p>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    @endauth

    {{-- EDIT HISTORY SECTION --}}
    <div class="card">
        <h2>Edit History</h2>
        @if($editHistory->isEmpty())
            <p class="muted">No edit history recorded yet.</p>
        @else
            <table>
                <thead>
                    <tr><th>Date</th><th>Action</th><th>Field</th><th>Old Value</th><th>New Value</th><th>By</th></tr>
                </thead>
                <tbody>
                    @foreach($editHistory as $entry)
                        <tr>
                            <td style="white-space: nowrap;">{{ $entry->created_at->format('M d, Y H:i') }}</td>
                            <td><span class="badge">{{ ucfirst($entry->action) }}</span></td>
                            <td>{{ ucfirst(str_replace('_', ' ', $entry->field_changed)) }}</td>
                            <td class="muted">{{ $entry->old_value ?? '—' }}</td>
                            <td>{{ $entry->new_value ?? '—' }}</td>
                            <td class="muted">{{ $entry->user->name ?? 'System' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- RELATED BOOKS SECTION --}}
    @if($related->count() > 0)
        <div class="card">
            <h2>More like this</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px;">
                @foreach($related as $rel)
                    <a href="{{ route('books.show', ['book' => $rel->id, 'back' => $backUrl]) }}" style="text-decoration: none; color: inherit;">
                        <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: white; cursor: pointer;"
                             onmouseover="this.style.borderColor='#111827'" onmouseout="this.style.borderColor='#ddd'">
                            <img src="{{ $rel->coverUrl() }}" alt="{{ $rel->title }}"
                                 loading="lazy"
                                 style="width: 100%; height: 160px; object-fit: cover; display: block; background: #e5e7eb;"
                                 onerror="this.onerror=null; this.src='{{ $rel->noImageSvg() }}'">
                            <div style="padding: 8px;">
                                <div style="font-weight: bold; font-size: 12px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $rel->title }}</div>
                                <div class="muted" style="font-size: 11px; margin-top: 2px;">{{ $rel->published_year }}</div>
                                <span class="badge {{ $rel->status === 'available' ? 'badge-green' : 'badge-red' }}" style="font-size: 10px; margin-top: 4px;">{{ ucfirst($rel->status) }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    let selectedRating = {{ $userReview ? $userReview->rating : 0 }};
    function hoverStars(n) { for (let i=1;i<=5;i++) document.getElementById('star-label-'+i).style.color = i<=n ? '#f59e0b' : '#d1d5db'; }
    function resetStars()  { for (let i=1;i<=5;i++) document.getElementById('star-label-'+i).style.color = i<=selectedRating ? '#f59e0b' : '#d1d5db'; }
    function selectStar(n) { selectedRating = n; }
</script>
@endpush