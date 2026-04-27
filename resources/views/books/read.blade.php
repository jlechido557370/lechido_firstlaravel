@extends('layouts.app')

@section('title', 'Reading: ' . $book->title)

@section('content')
@php
    $backTarget = $backUrl ?: route('books.show', ['book' => $book->id]);
@endphp

<div class="card" style="margin-bottom: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
        <div>
            <a href="{{ $backTarget }}">&larr; Back</a>
            <h2 style="margin: 4px 0 2px;">{{ $book->title }}</h2>
            <span class="muted">by {{ $book->author }}</span>
        </div>
        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <span class="badge badge-green" style="font-size: 13px; padding: 6px 12px;">Currently Borrowed</span>
            @if($previewLink)
                <a href="{{ $previewLink }}" target="_blank"
                   style="padding: 7px 14px; background: #4285f4; color: white; border-radius: 6px; font-size: 13px; text-decoration: none;">
                    Open full view on Google Books &nearr;
                </a>
            @endif
        </div>
    </div>
</div>

@if($googleFound)
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="background: #f3f4f6; padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-size: 13px; color: #6b7280;">
            Powered by Google Books. Not all pages may be available depending on publisher permissions.
            &bull; <a href="{{ $previewLink }}" target="_blank">Open full view on Google Books &nearr;</a>
        </div>
        <div id="viewerCanvas" style="width: 100%; height: 82vh; background: #525659;"></div>
        <div id="viewerFallback" style="display:none; padding: 40px; text-align: center; background: #525659; color: #ccc;">
            <p style="font-size: 16px; margin-bottom: 16px;">Preview is not available for this edition of <strong style="color:white;">{{ $book->title }}</strong>.</p>
            <p style="font-size: 13px; margin-bottom: 20px;">The publisher has restricted online preview for this book. Try the alternatives below.</p>
            <a href="{{ $previewLink }}" target="_blank"
               style="padding: 9px 18px; background: #4285f4; color: white; border-radius: 6px; text-decoration: none; font-size: 14px; margin-right: 8px;">
                Open on Google Books &nearr;
            </a>
        </div>
    </div>
@elseif(isset($readUrl))
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="background: #f3f4f6; padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-size: 13px; color: #6b7280;">
            Direct reading source found.
        </div>
        @if($isPdf)
            <iframe src="https://docs.google.com/gview?url={{ urlencode($readUrl) }}&embedded=true" style="width: 100%; height: 82vh; border: none;"></iframe>
        @else
            <iframe src="{{ $readUrl }}" style="width: 100%; height: 82vh; border: none;"></iframe>
        @endif
    </div>
@else
    <div class="card">
        <h3 style="margin-bottom: 8px;">Online reading not available</h3>
        <p class="muted" style="margin-bottom: 16px;">We couldn't find an online version of <strong>{{ $book->title }}</strong> on Google Books. Try the alternatives below.</p>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; padding: 40px;">
            <div class="spinner" style="width: 32px; height: 32px; border: 3px solid var(--border); border-top: 3px solid var(--accent); border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <p style="color: var(--muted);">Loading reader...</p>
        </div>
    </div>
@endif

<div class="card">
    <h3 style="margin-bottom: 12px;">Alternative Reading Sources</h3>
    <p class="muted" style="margin-bottom: 12px; font-size: 13px;">If the preview above is blank or limited, these free resources may have a readable version:</p>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="https://archive.org/search?query=isbn:{{ preg_replace('/[^0-9X]/', '', strtoupper($book->isbn)) }}" target="_blank" style="padding: 9px 18px; background: #111827; color: white; border-radius: 6px; text-decoration: none; font-size: 14px;">Search Internet Archive</a>
        <a href="https://openlibrary.org/search?isbn={{ preg_replace('/[^0-9X]/', '', strtoupper($book->isbn)) }}" target="_blank" style="padding: 9px 18px; background: #1a56db; color: white; border-radius: 6px; text-decoration: none; font-size: 14px;">Open Library</a>
        <a href="https://www.gutenberg.org/ebooks/search/?query={{ urlencode($book->title) }}" target="_blank" style="padding: 9px 18px; border: 1px solid #111827; color: #111827; border-radius: 6px; text-decoration: none; font-size: 14px;">Project Gutenberg</a>
        <a href="https://www.google.com/search?q={{ urlencode('"' . $book->title . '" "' . $book->author . '"') }}+read+online+free" target="_blank" style="padding: 9px 18px; border: 1px solid #4285f4; color: #4285f4; border-radius: 6px; text-decoration: none; font-size: 14px;">Search Online</a>
    </div>
    <p class="muted" style="font-size: 12px; margin-top: 10px;">Availability depends on copyright status. Older books (pre-1928) are typically freely available.</p>
</div>
@endsection

@push('scripts')
@if($googleFound)
<script src="https://www.google.com/books/jsapi.js"></script>
<script>
(function () {
    var volumeId = '{{ $googleBooksId }}';
    var isbn     = '{{ preg_replace('/[^0-9X]/', '', strtoupper($book->isbn)) }}';

    function showFallback() {
        document.getElementById('viewerCanvas').style.display = 'none';
        document.getElementById('viewerFallback').style.display = 'block';
    }

    function initialize() {
        var viewer = new google.books.DefaultViewer(document.getElementById('viewerCanvas'));

        function notFound() {
            if (isbn) {
                viewer.load('ISBN:' + isbn, showFallback);
            } else {
                showFallback();
            }
        }

        viewer.load(volumeId, notFound);

        setTimeout(function () {
            var canvas  = document.getElementById('viewerCanvas');
            var fb      = document.getElementById('viewerFallback');
            if (!canvas || fb.style.display !== 'none') return;
            var imgs = canvas.querySelectorAll('img');
            var text = canvas.innerText || '';
            var hasNoPages = text.indexOf('No pages') !== -1 || text.indexOf('not available') !== -1 || text.indexOf('Preview not available') !== -1;
            if (imgs.length === 0 || hasNoPages) {
                showFallback();
            }
        }, 7000);
    }

    google.books.load();
    google.books.setOnLoadCallback(initialize);
})();
</script>
@endif
@endpush
