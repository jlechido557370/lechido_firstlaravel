@extends('layouts.app')

@section('title', ($user->id === auth()->id() ? 'My Ratings' : $user->name . "'s Ratings"))

@section('content')
    @php
        $isOwn = auth()->check() && auth()->id() === $user->id;
        $backUrl = request('back') ?: ($isOwn ? route('user.profile') : route('user.public_profile', $user->id));
    @endphp

    <div class="card" style="padding:28px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
            <a href="{{ $backUrl }}" style="display:inline-flex; align-items:center; gap:4px; color:var(--muted); font-size:13px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                Back
            </a>
        </div>
        <h1 style="font-size:24px; font-weight:600; margin-bottom:4px;">{{ $isOwn ? 'My Ratings' : $user->name . "'s Ratings" }}</h1>
        <p class="muted">{{ $ratings->count() }} rated book{{ $ratings->count() === 1 ? '' : 's' }}</p>
        @if($isOwn)
            <p style="margin-top:8px;"><a href="{{ route('user.public_profile', $user->id) }}" style="font-size:13px;">View public profile</a></p>
        @endif
    </div>

    @if($ratings->isEmpty())
        <div class="card" style="padding:48px 28px; text-align:center;">
            <div style="font-size:36px; margin-bottom:12px;">⭐</div>
            <h2 style="font-size:18px; font-weight:600; margin-bottom:8px; color:var(--black);">No ratings yet</h2>
            <p class="muted" style="max-width:400px; margin:0 auto;">
                {{ $isOwn ? 'Start rating books you read to build your reading profile.' : 'This user hasn\'t rated any books yet.' }}
            </p>
            @if($isOwn)
                <a href="{{ route('books.catalogue') }}" style="display:inline-flex; align-items:center; gap:6px; margin-top:16px; padding:10px 20px; background:var(--black); color:var(--white); border-radius:8px; font-size:14px; font-weight:500; text-decoration:none;">
                    Browse Books
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            @endif
        </div>
    @else
        <div class="card" style="padding:0; overflow:hidden;">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left:24px;">Book</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th style="padding-right:24px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ratings as $rating)
                        <tr>
                            <td style="padding-left:24px;">
                                <a href="{{ route('books.show', ['book' => $rating->book_id, 'back' => request()->fullUrl()]) }}" style="font-weight:500;">{{ $rating->book->title ?? 'Deleted Book' }}</a>
                                <div class="muted" style="font-size:12px; margin-top:2px;">{{ $rating->book->author ?? '' }}</div>
                            </td>
                            <td>
                                <span style="font-size:15px; color:#f59e0b; letter-spacing:1px;">{{ str_repeat('★', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}</span>
                                <span style="font-family:var(--font-mono); font-size:12px; color:var(--muted); margin-left:4px;">{{ $rating->rating }}/5</span>
                            </td>
                            <td style="color:var(--muted); font-size:14px; max-width:300px;">{{ $rating->comment ?: '—' }}</td>
                            <td style="padding-right:24px; color:var(--muted); font-size:12px; font-family:var(--font-mono); white-space:nowrap;">{{ $rating->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

