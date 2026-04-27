@extends('layouts.app')

@section('title', 'Following')

@section('content')
    <div class="card" style="padding:28px;">
        <h1 style="font-size:24px; font-weight:600; margin-bottom:4px;">Following</h1>
        <p class="muted">People and authors you follow</p>
    </div>

    <div class="grid grid-2">
        <div class="card" style="padding:28px;">
            <h2 style="font-size:17px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Users I Follow
            </h2>
            @forelse($followingUsers as $followed)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--mid);">
                    <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                        <img src="{{ $followed->avatarUrl() }}" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:1.5px solid var(--border); flex-shrink:0;">
                        <div style="min-width:0;">
                            <a href="{{ route('user.public_profile', $followed->id) }}" style="font-weight:500; font-size:15px; color:var(--black);">{{ $followed->displayName() }}</a>
                            <div class="muted" style="font-size:12px;">Member since {{ $followed->created_at->format('M Y') }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('follow.user', $followed->id) }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn-outline" style="padding:6px 14px; font-size:12px;">Unfollow</button>
                    </form>
                </div>
            @empty
                <div style="text-align:center; padding:32px 0;">
                    <div style="font-size:32px; margin-bottom:10px; opacity:.5;">👥</div>
                    <p class="muted" style="margin-bottom:4px;">You are not following any users yet.</p>
                    <a href="{{ route('search') }}" style="font-size:13px;">Find users to follow</a>
                </div>
            @endforelse
        </div>

        <div class="card" style="padding:28px;">
            <h2 style="font-size:17px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Authors I Follow
            </h2>
            @forelse($followingAuthors as $af)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--mid);">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:36px; height:36px; border-radius:50%; background:var(--off); border:1.5px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0;">✍</div>
                        <strong style="font-size:15px; color:var(--black);">{{ $af->author_name }}</strong>
                    </div>
                    <form method="POST" action="{{ route('follow.author') }}" style="margin:0;">
                        @csrf
                        <input type="hidden" name="author_name" value="{{ $af->author_name }}">
                        <button type="submit" class="btn-outline" style="padding:6px 14px; font-size:12px;">Unfollow</button>
                    </form>
                </div>
            @empty
                <div style="text-align:center; padding:32px 0;">
                    <div style="font-size:32px; margin-bottom:10px; opacity:.5;">✍️</div>
                    <p class="muted" style="margin-bottom:4px;">You are not following any authors yet.</p>
                    <a href="{{ route('books.catalogue') }}" style="font-size:13px;">Browse authors</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection

