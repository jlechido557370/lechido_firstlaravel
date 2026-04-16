@extends('layouts.app')

@section('title', 'Reading: ' . $book->title)

@section('content')
<div class="card" style="margin-bottom: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
        <div>
            <a href="{{ route('books.show', $book->id) }}">&larr; Back to book details</a>
            <h2 style="margin: 4px 0 2px;">{{ $book->title }}</h2>
            <span class="muted">by {{ $book->author }}</span>
        </div>
        <span class="badge badge-green" style="font-size: 13px; padding: 6px 12px;">Currently Borrowed</span>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    {{-- If read_url is a PDF or embeddable page --}}
    @if(str_ends_with(strtolower(parse_url($book->read_url, PHP_URL_PATH) ?? ''), '.pdf'))
        {{-- PDF viewer --}}
        <div style="width: 100%; height: 85vh; background: #525659;">
            <object data="{{ $book->read_url }}" type="application/pdf" width="100%" height="100%">
                <p style="padding: 20px; color: white;">
                    Your browser cannot display the PDF inline.
                    <a href="{{ $book->read_url }}" target="_blank" style="color: #93c5fd;">Click here to open it in a new tab.</a>
                </p>
            </object>
        </div>
    @else
        {{-- Iframe for external links (e.g. Project Gutenberg, Google Books) --}}
        <div style="background: #f3f4f6; padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-size: 13px; color: #6b7280;">
            Reading from: <a href="{{ $book->read_url }}" target="_blank">{{ $book->read_url }}</a>
            &nbsp;&bull;&nbsp;
            <a href="{{ $book->read_url }}" target="_blank">Open in new tab &nearr;</a>
        </div>
        <iframe
            src="{{ $book->read_url }}"
            width="100%"
            style="height: 85vh; border: none; display: block;"
            sandbox="allow-scripts allow-same-origin allow-forms allow-popups"
            title="Reading {{ $book->title }}">
        </iframe>
    @endif
</div>
@endsection