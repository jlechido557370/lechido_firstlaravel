@extends('layouts.app')

@section('title', 'Following')

@section('content')
    <div class="card">
        <h1>Following</h1>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <h2>Users I Follow</h2>
            @forelse($followingUsers as $followed)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img src="{{ $followed->avatarUrl() }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                        <div>
                            <a href="{{ route('user.public_profile', $followed->id) }}">{{ $followed->displayName() }}</a>
                            <div class="muted" style="font-size:12px;">Member since {{ $followed->created_at->format('M Y') }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('follow.user', $followed->id) }}">
                        @csrf
                        <button type="submit" style="width:auto;padding:5px 10px;font-size:13px;">Unfollow</button>
                    </form>
                </div>
            @empty
                <p class="muted">You are not following any users yet.</p>
            @endforelse
        </div>

        <div class="card">
            <h2>Authors I Follow</h2>
            @forelse($followingAuthors as $af)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                    <div>
                        <strong>{{ $af->author_name }}</strong>
                    </div>
                    <form method="POST" action="{{ route('follow.author') }}">
                        @csrf
                        <input type="hidden" name="author_name" value="{{ $af->author_name }}">
                        <button type="submit" style="width:auto;padding:5px 10px;font-size:13px;">Unfollow</button>
                    </form>
                </div>
            @empty
                <p class="muted">You are not following any authors yet.</p>
            @endforelse
        </div>
    </div>
@endsection