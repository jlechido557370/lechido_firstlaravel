@extends('layouts.app')

@section('title', $user->publicDisplayName() . "'s Profile")

@section('content')
    @php $backUrl = request('back') ?: route('home'); @endphp

    {{-- Hero Header --}}
    <div class="card" style="padding:0; overflow:hidden; position:relative;">
        <div style="background: linear-gradient(135deg, #0f172a 0%, #111827 100%); padding:36px 40px; position:relative; overflow:hidden;">
            <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;border-radius:50%;border:1px solid rgba(255,255,255,.05);pointer-events:none;"></div>
            <div style="position:absolute;top:-20px;right:-20px;width:140px;height:140px;border-radius:50%;border:1px solid rgba(255,255,255,.07);pointer-events:none;"></div>

            <div style="display:flex; align-items:center; gap:24px; flex-wrap:wrap; position:relative; z-index:1;">
                <div style="position:relative; flex-shrink:0;">
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->publicDisplayName() }}"
                         style="width:96px; height:96px; border-radius:50%; object-fit:cover; border:2.5px solid rgba(255,255,255,.25); box-shadow:0 4px 24px rgba(0,0,0,.4);">
                    @if($user->isSubscribed())
                        <div style="position:absolute; bottom:2px; right:2px; width:24px; height:24px; background:#ffffff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; border:2px solid #0f172a;">✦</div>
                    @endif
                </div>
                <div style="flex:1; min-width:0;">
                    <h1 style="font-size:26px; font-weight:500; color:#ffffff; margin:0 0 4px;">{{ $user->badgedName() }}</h1>
                    @if(!$user->hide_real_name && $user->name !== $user->username && $user->username)
                        <div style="font-size:13px; color:rgba(255,255,255,.6); margin-bottom:4px;">{{ $user->name }}</div>
                    @endif
                    <div style="font-size:12px; color:rgba(255,255,255,.55); font-family:var(--font-mono); letter-spacing:.04em; text-transform:uppercase;">
                        Member since {{ $user->created_at->format('F Y') }}
                        @if($user->gender && $user->gender !== 'prefer_not_to_say')
                            &nbsp;&bull;&nbsp; {{ ucfirst($user->gender) }}
                        @endif
                    </div>
                    @if($user->isSubscribed())
                        <div style="display:inline-block; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); padding:3px 10px; font-size:11px; font-family:var(--font-mono); letter-spacing:.05em; color:rgba(255,255,255,.88); margin-top:8px; border-radius:4px;">
                            SUBSCRIBER
                        </div>
                    @endif
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap; flex-shrink:0;">
                    @auth
                        @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('follow.user', $user->id) }}">
                                @csrf
                                <button type="submit" style="padding:8px 18px; font-size:13px; border-radius:8px; background:rgba(255,255,255,.12); color:#fff; border:1px solid rgba(255,255,255,.2); backdrop-filter:blur(4px);">
                                    {{ $isFollowing ? 'Unfollow' : 'Follow' }}
                                </button>
                            </form>
                            @if($user->allow_dms && !$isBlocking)
                                <a href="{{ route('messages.conversation', $user->id) }}" style="display:inline-flex; align-items:center; gap:6px; padding:8px 18px; background:#ffffff; color:#0f172a; border-radius:8px; font-size:13px; font-weight:500; text-decoration:none;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    Message
                                </a>
                            @endif
                        @else
                            <a href="{{ route('user.profile') }}" style="display:inline-flex; align-items:center; gap:6px; padding:8px 18px; background:rgba(255,255,255,.12); color:#fff; border:1px solid rgba(255,255,255,.2); border-radius:8px; font-size:13px; text-decoration:none;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                Edit Profile
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        {{-- Stats Bar --}}
        <div style="display:flex; border-top:1px solid var(--border); background:var(--off);">
            <div style="flex:1; text-align:center; padding:16px; border-right:1px solid var(--border);">
                <div style="font-size:22px; font-weight:600; color:var(--black); font-family:var(--font-disp);">{{ $user->followers()->count() }}</div>
                <div style="font-size:11px; color:var(--muted); font-family:var(--font-mono); text-transform:uppercase; letter-spacing:.06em;">Followers</div>
            </div>
            <div style="flex:1; text-align:center; padding:16px; border-right:1px solid var(--border);">
                <div style="font-size:22px; font-weight:600; color:var(--black); font-family:var(--font-disp);">{{ $user->following()->count() }}</div>
                <div style="font-size:11px; color:var(--muted); font-family:var(--font-mono); text-transform:uppercase; letter-spacing:.06em;">Following</div>
            </div>
            <div style="flex:1; text-align:center; padding:16px; border-right:1px solid var(--border);">
                <div style="font-size:22px; font-weight:600; color:var(--black); font-family:var(--font-disp);">{{ $borrowCount }}</div>
                <div style="font-size:11px; color:var(--muted); font-family:var(--font-mono); text-transform:uppercase; letter-spacing:.06em;">Books Borrowed</div>
            </div>
            <div style="flex:1; text-align:center; padding:16px;">
                <div style="font-size:22px; font-weight:600; color:var(--black); font-family:var(--font-disp);">{{ $ratings->count() }}</div>
                <div style="font-size:11px; color:var(--muted); font-family:var(--font-mono); text-transform:uppercase; letter-spacing:.06em;">Ratings</div>
            </div>
        </div>
    </div>

    {{-- Bio --}}
    @if($user->bio)
        <div class="card" style="padding:28px;">
            <div style="font-size:10px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; font-family:var(--font-mono); color:var(--muted); margin-bottom:10px;">About</div>
            <p style="line-height:1.75; color:var(--black); font-size:15px; margin:0; max-width:680px;">{{ $user->bio }}</p>
        </div>
    @endif

    {{-- Ratings --}}
    @if($ratings->count())
        <div class="card" style="padding:28px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                <h2 style="font-size:18px; font-weight:600; margin:0;">Recent Ratings</h2>
                <a href="{{ route('user.public_ratings', $user->id) }}" style="font-size:13px; color:var(--muted);">View all →</a>
            </div>
            <div>
                @foreach($ratings as $rating)
                    <div style="padding:16px 0; border-bottom:1px solid var(--mid);">
                        <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:flex-start;">
                            <div>
                                <a href="{{ route('books.show', ['book' => $rating->book_id]) }}" style="font-weight:600; font-size:15px; color:var(--black);">{{ $rating->book->title ?? 'Deleted Book' }}</a>
                                <div class="muted" style="font-size:13px; margin-top:2px;">{{ $rating->book->author ?? '' }}</div>
                            </div>
                            <div style="font-size:12px; color:var(--muted); font-family:var(--font-mono); white-space:nowrap;">{{ $rating->created_at->format('M d, Y') }}</div>
                        </div>
                        <div style="margin-top:6px; color:#f59e0b; font-size:16px; letter-spacing:1px;">{{ str_repeat('★', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}</div>
                        @if($rating->comment)
                            <p style="margin:8px 0 0; line-height:1.65; font-size:14px; color:var(--black);">{{ $rating->comment }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection

