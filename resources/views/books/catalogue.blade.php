@extends('layouts.app')

@section('title', 'Catalogue')

@section('content')
    <div class="card" style="padding:28px;">
        <h1 style="font-size:24px; font-weight:600; margin-bottom:4px;">Book Catalogue</h1>
        <p class="muted">{{ $totalBooks }} books across {{ $genres->count() }} subjects. Click any book to see details.</p>
        <div style="margin-top:14px; display:flex; flex-wrap:wrap; gap:6px;">
            @foreach($genres as $g)
                <a href="#genre-{{ Str::slug($g) }}" style="display:inline-block;">
                    <span class="badge" style="font-size:13px; padding:7px 14px; transition:background .15s, color .15s;">{{ $g }} ({{ count($byGenre[$g]) }})</span>
                </a>
            @endforeach
        </div>
    </div>

    @foreach($genres as $genre)
        <div class="card" id="genre-{{ Str::slug($genre) }}" style="padding:0; overflow:hidden;">
            <div style="padding:20px 24px 16px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; background:var(--off);">
                <h2 style="margin:0; font-size:18px; font-weight:600; color:var(--black);">{{ $genre }}</h2>
                <a href="{{ route('home', ['genre' => $genre]) }}" style="font-size:13px; color:var(--muted); display:inline-flex; align-items:center; gap:4px;">
                    Filter by {{ $genre }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="padding-left:24px;">Title</th>
                        <th>Author</th>
                        <th>Year</th>
                        <th>Copies</th>
                        <th style="padding-right:24px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byGenre[$genre] as $book)
                        <tr style="cursor:pointer;" onclick="window.location='{{ route('books.show', ['book' => $book->id, 'back' => request()->fullUrl()]) }}'">
                            <td style="padding-left:24px;">
                                <a href="{{ route('books.show', ['book' => $book->id, 'back' => request()->fullUrl()]) }}" style="font-weight:500;">{{ $book->title }}</a>
                            </td>
                            <td>{{ $book->author }}</td>
                            <td style="font-family:var(--font-mono); font-size:13px; color:var(--muted);">{{ $book->published_year }}</td>
                            <td>
                                <strong style="color:{{ $book->available_copies > 0 ? '#16a34a' : '#dc2626' }}">{{ $book->available_copies }}</strong>
                                <span class="muted" style="font-size:12px;"> / {{ $book->total_copies }}</span>
                            </td>
                            <td style="padding-right:24px;">
                                <span class="badge {{ $book->status === 'available' ? 'badge-green' : 'badge-red' }}">
                                    {{ ucfirst($book->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
@endsection

