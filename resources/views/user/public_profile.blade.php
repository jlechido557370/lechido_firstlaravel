@extends('layouts.app')

@section('title', $user->name . "'s Profile")

@section('content')
    <div class="card">
        <p style="margin-bottom: 12px;"><a href="javascript:history.back()">&larr; Back</a></p>

        <div style="display: flex; align-items: flex-start; gap: 20px; flex-wrap: wrap;">
            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #e5e7eb; flex-shrink: 0;">
            <div>
                <h1 style="margin: 0 0 4px;">{{ $user->name }}</h1>
                <p class="muted" style="margin: 0 0 8px;">{{ ucfirst($user->role) }} &bull; Member since {{ $user->created_at->format('F Y') }}</p>

                @if($user->bio)
                    <p style="line-height: 1.6; max-width: 600px;">{{ $user->bio }}</p>
                @else
                    <p class="muted" style="font-style: italic;">This user hasn't written a bio yet.</p>
                @endif

                <p class="muted" style="font-size: 13px; margin-top: 8px;">
                    Books borrowed: <strong>{{ $borrowCount }}</strong>
                </p>
            </div>
        </div>
    </div>

    @auth
        @if(auth()->id() === $user->id)
            <div class="card">
                <p>This is your public profile. <a href="{{ route('user.profile') }}">Edit your profile</a></p>
            </div>
        @endif
    @endauth
@endsection