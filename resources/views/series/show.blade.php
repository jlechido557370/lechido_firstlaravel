@extends('layouts.app')

@section('title', $series->title)

@section('content')
<div class="card">
    <p><a href="{{ route('home') }}">&larr; Back to Home</a></p>
    <h1>{{ $series->title }}</h1>
    <p class="muted">by {{ $series->author ?? 'Unknown' }}</p>
    <p>Type: <span class="badge">{{ ucfirst($series->book_type) }}</span></p>
    @if($series->description)
        <p>{{ $series->description }}</p>
    @endif

    @auth
        <form method="POST" action="{{ route('series.toggleList', $series) }}" style="margin: 12px 0;">
            @csrf
            <button type="submit" style="width: auto; padding: 8px 16px;">
                {{ $isListed ? 'Listed' : 'List' }}
            </button>
        </form>
    @endauth
</div>

@if($volumes->count() > 0)
<div class="card">
    <h2>Volumes</h2>
    <ul>
        @foreach($volumes as $volume)
            <li>
                Volume {{ $volume->volume_number }} – 
                @if($volume->read_url)
                    <a href="{{ $volume->read_url }}" target="_blank">{{ $volume->title }}</a>
                @else
                    <a href="{{ route('books.show', $volume) }}">{{ $volume->title }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endif

@if($chapters->count() > 0)
<div class="card">
    <h2>Chapters</h2>
    <ul>
        @foreach($chapters as $chapter)
            <li>
                Chapter {{ $chapter->chapter_number }} – 
                @if($chapter->read_url)
                    <a href="{{ $chapter->read_url }}" target="_blank">{{ $chapter->title }}</a>
                @else
                    <a href="{{ route('books.show', $chapter) }}">{{ $chapter->title }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endif

@if($volumes->isEmpty() && $chapters->isEmpty())
    <div class="card">
        <p class="muted">No volumes or chapters have been added to this series yet.</p>
    </div>
@endif
@endsection