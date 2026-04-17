@extends('layouts.app')

@section('title', ($user->id === auth()->id() ? 'My Ratings' : $user->name . "'s Ratings"))

@section('content')
    @php
        $isOwn = auth()->check() && auth()->id() === $user->id;
        $backUrl = request('back') ?: ($isOwn ? route('user.profile') : route('user.public_profile', $user->id));
    @endphp

    <div class="card">
        <p style="margin-bottom: 12px;"><a href="{{ $backUrl }}">&larr; Back</a></p>
        <h1>{{ $isOwn ? 'My Ratings' : $user->name . "'s Ratings" }}</h1>
        <p class="muted">{{ $ratings->count() }} rated book{{ $ratings->count() === 1 ? '' : 's' }}</p>
        @if($isOwn)
            <p><a href="{{ route('user.public_profile', $user->id) }}">View public profile</a></p>
        @endif
    </div>

    @if($ratings->isEmpty())
        <div class="card">
            <p class="muted">No ratings yet.</p>
        </div>
    @else
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ratings as $rating)
                        <tr>
                            <td><a href="{{ route('books.show', ['book' => $rating->book_id, 'back' => request()->fullUrl()]) }}">{{ $rating->book->title ?? 'Deleted Book' }}</a></td>
                            <td>{{ $rating->rating }}/5</td>
                            <td>{{ $rating->comment ?: '—' }}</td>
                            <td>{{ $rating->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
