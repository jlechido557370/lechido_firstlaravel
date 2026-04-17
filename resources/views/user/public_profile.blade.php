@extends('layouts.app')

@section('title', $user->displayName() . "'s Profile")

@section('content')
    @php $backUrl = request('back') ?: route('home'); @endphp
    <div class="card">
        <p style="margin-bottom: 12px;"><a href="{{ $backUrl }}">&larr; Back</a></p>

        <div style="display: flex; align-items: flex-start; gap: 20px; flex-wrap: wrap;">
            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->displayName() }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #e5e7eb; flex-shrink: 0;">
            <div style="flex:1; min-width:200px;">
                <h1 style="margin: 0 0 4px;">{{ $user->displayName() }}</h1>
                @if($user->name !== $user->username && $user->username)
                    <div class="muted" style="font-size:13px; margin-bottom:4px;">{{ $user->name }}</div>
                @endif
                <p class="muted" style="margin: 0 0 8px;">
                    Member since {{ $user->created_at->format('F Y') }}
                    @if($user->gender && $user->gender !== 'prefer_not_to_say')
                        &bull; {{ ucfirst($user->gender) }}
                    @endif
                </p>
                <p class="muted" style="font-size:13px; margin:4px 0;">
                    Followers: <strong>{{ $user->followers()->count() }}</strong>
                    &nbsp;&bull;&nbsp;
                    Following: <strong>{{ $user->following()->count() }}</strong>
                </p>

                @if($user->bio)
                    <p style="line-height: 1.6; max-width: 600px; margin:8px 0;">{{ $user->bio }}</p>
                @else
                    <p class="muted" style="font-style: italic;">This user hasn't written a bio yet.</p>
                @endif

                <p class="muted" style="font-size: 13px; margin-top: 8px;">Books borrowed: <strong>{{ $borrowCount }}</strong></p>
                <p style="margin-top: 10px;"><a href="{{ route('user.public_ratings', $user->id) }}">View all ratings</a></p>

                @auth
                    @if(auth()->id() !== $user->id)
                        <div style="display:flex; gap:8px; margin-top:12px; flex-wrap:wrap;">
                            <form method="POST" action="{{ route('follow.user', $user->id) }}">
                                @csrf
                                <button type="submit" style="width:auto; padding:7px 14px; font-size:13px;">
                                    {{ $isFollowing ? 'Unfollow' : 'Follow' }}
                                </button>
                            </form>
                            @if($user->allow_dms)
                                <a href="{{ route('messages.conversation', $user->id) }}" style="display:inline-block; padding:7px 14px; background:#111827; color:white; border-radius:6px; font-size:13px; text-decoration:none;">
                                    Message
                                </a>
                            @else
                                <span style="font-size:13px; color:#6b7280; padding:7px 0;">Messages disabled</span>
                            @endif
                        </div>
                    @else
                        <div style="margin-top:12px;">
                            <a href="{{ route('user.profile') }}" style="font-size:13px;">Edit your profile</a>
                        </div>
                    @endif
                @endauth
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
                                <a href="{{ route('books.show', ['book' => $rating->book_id]) }}" style="font-weight: 700;">{{ $rating->book->title ?? 'Deleted Book' }}</a>
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
@endsection