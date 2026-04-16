@extends('layouts.app')

@section('title', 'Catalogue')

@section('content')
    <div class="card">
        <h1>Book Catalogue</h1>
        <p class="muted">{{ $totalBooks }} books across {{ $genres->count() }} subjects. Click any book to see details.</p>
        <div style="margin-top: 8px;">
            @foreach($genres as $g)
                <a href="#genre-{{ Str::slug($g) }}" style="display: inline-block; margin: 4px 6px 4px 0;">
                    <span class="badge" style="font-size: 13px; padding: 6px 12px;">{{ $g }} ({{ count($byGenre[$g]) }})</span>
                </a>
            @endforeach
        </div>
    </div>

    @foreach($genres as $genre)
        <div class="card" id="genre-{{ Str::slug($genre) }}">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h2 style="margin: 0;">{{ $genre }}</h2>
                <a href="{{ route('home', ['genre' => $genre]) }}" class="muted" style="font-size: 13px;">
                    Filter by {{ $genre }} &rarr;
                </a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Year</th>
                        <th>Copies</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byGenre[$genre] as $book)
                        <tr style="cursor: pointer;" onclick="window.location='{{ route('books.show', $book->id) }}'">
                            <td>
                                <a href="{{ route('books.show', $book->id) }}">{{ $book->title }}</a>
                            </td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $book->published_year }}</td>
                            <td>{{ $book->available_copies }}/{{ $book->total_copies }}</td>
                            <td>
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