@extends('layouts.app')

@section('title', $user->name . "'s Profile")

@section('content')
    @php $backUrl = request('back') ?: route('home'); @endphp
    <div class="card">
        <p style="margin-bottom: 12px;"><a href="{{ $backUrl }}">&larr; Back</a></p>

        <div style="display: flex; align-items: flex-start; gap: 20px; flex-wrap: wrap;">
            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #e5e7eb; flex-shrink: 0;">
            <div>
                <h1 style="margin: 0 0 4px;">{{ $user->name }}</h1>
                <p class="muted" style="margin: 0 0 8px;">{{ ucfirst($user->role) }} &bull; Member since {{ $user->created_at->format('F Y') }}</p>

                @if($user->bio)
                    <p style="line-height: 1.6; max-width: 600px;">{{ $user->bio }}</p>
                @else
                    <p class="muted" style="font-style: italic;">This user hasn't written a bio yet.</p>
                @endif

                <p class="muted" style="font-size: 13px; margin-top: 8px;">Books borrowed: <strong>{{ $borrowCount }}</strong></p>
                <p style="margin-top: 10px;"><a href="{{ route('user.public_ratings', $user->id) }}">View all ratings</a></p>
            </div>
        </div>
    </div>

    @if($ratings->count())
        <div class="card">
            <h2>Recent Ratings</h2>
            <div>
                @foreach($ratings as $rating)
                    <div style="padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                            <div>
                                <a href="{{ route('books.show', ['book' => $rating->book_id, 'back' => request()->fullUrl()]) }}" style="font-weight: 700;">{{ $rating->book->title ?? 'Deleted Book' }}</a>
                                <div class="muted" style="font-size: 13px;">{{ $rating->book->author ?? '' }}</div>
                            </div>
                            <div class="muted" style="font-size: 13px;">{{ $rating->created_at->format('M d, Y') }}</div>
                        </div>
                        <div style="margin-top: 4px; color: #f59e0b;">{{ str_repeat('★', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}</div>
                        @if($rating->comment)
                            <p style="margin: 6px 0 0; line-height: 1.6;">{{ $rating->comment }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @auth
        @if(auth()->id() === $user->id)
            <div class="card">
                <p>This is your public profile. <a href="{{ route('user.profile') }}">Edit your profile</a></p>
            </div>
        @endif
    @endauth
@endsection
