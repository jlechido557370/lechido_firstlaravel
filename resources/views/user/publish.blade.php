@extends('layouts.app')

@section('title', 'Publish a Book')

@section('content')
<style>
    .publish-card {
        max-width: 620px;
        margin: 0 auto;
    }
    .form-section {
        margin-bottom: 24px;
    }
    .form-section-title {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        font-family: var(--font-mono);
        color: var(--muted);
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border);
    }
    .cover-preview {
        width: 120px;
        height: 170px;
        object-fit: cover;
        border-radius: 8px;
        border: 1.5px solid var(--border);
        background: var(--mid);
        display: none;
    }
    .cover-preview.visible { display: block; }
</style>

<div class="card publish-card" style="padding:32px;">
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
        <a href="{{ route('user.dashboard') }}" style="display:inline-flex; align-items:center; gap:4px; color:var(--muted); font-size:13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
            Dashboard
        </a>
    </div>
    <h1 style="font-size:24px; font-weight:600; margin-bottom:4px;">Publish a Book</h1>
    <p class="muted" style="margin-bottom:24px;">Submit a new book to the library catalogue for staff review.</p>

    <form method="POST" action="{{ route('user.publish') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-section">
            <div class="form-section-title">Book Details</div>
            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Title <span style="color:#dc2626;">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Enter book title">
                @error('title')<div style="color:#dc2626; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Author <span style="color:#dc2626;">*</span></label>
                <input type="text" name="author" value="{{ old('author') }}" required placeholder="Enter author name">
                @error('author')<div style="color:#dc2626; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div style="margin-bottom:16px;">
                    <label style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Genre <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="genre" value="{{ old('genre') }}" required placeholder="e.g. Fiction">
                    @error('genre')<div style="color:#dc2626; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom:16px;">
                    <label style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Published Year <span style="color:#dc2626;">*</span></label>
                    <input type="number" name="published_year" value="{{ old('published_year') }}" required placeholder="{{ date('Y') }}">
                    @error('published_year')<div style="color:#dc2626; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">ISBN</label>
                <input type="text" name="isbn" value="{{ old('isbn') }}" placeholder="e.g. 9780132350884">
                @error('isbn')<div style="color:#dc2626; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">Description</label>
                <textarea name="description" rows="4" placeholder="Brief description of the book..." style="resize:vertical;">{{ old('description') }}</textarea>
                @error('description')<div style="color:#dc2626; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title">Cover Image</div>
            <div style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">
                <div style="flex:1; min-width:200px;">
                    <input type="file" name="cover" accept="image/*" id="coverInput" style="margin-bottom:10px;">
                    <div style="font-size:12px; color:var(--muted); line-height:1.6;">
                        JPG, PNG, GIF, WEBP — max 2MB.<br>
                        Recommended: 3:4 ratio (e.g. 300x400px).
                    </div>
                    @error('cover')<div style="color:#dc2626; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <img id="coverPreview" class="cover-preview" alt="Cover preview">
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title">Read URL (Optional)</div>
            <div style="margin-bottom:16px;">
                <input type="url" name="read_url" value="{{ old('read_url') }}" placeholder="https://example.com/book.pdf">
                @error('read_url')<div style="color:#dc2626; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                <div style="font-size:12px; color:var(--muted); margin-top:6px;">Provide a direct link to an online readable version of the book.</div>
            </div>
        </div>

        <div style="display:flex; gap:12px; align-items:center; padding-top:8px; border-top:1px solid var(--border);">
            <button type="submit" style="padding:12px 28px; font-size:14px; display:inline-flex; align-items:center; gap:8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Submit for Review
            </button>
            <a href="{{ route('user.dashboard') }}" class="btn-outline" style="padding:12px 24px; font-size:14px; text-decoration:none; display:inline-flex; align-items:center;">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('coverInput')?.addEventListener('change', function(e) {
    var file = e.target.files[0];
    var preview = document.getElementById('coverPreview');
    if (file && preview) {
        var reader = new FileReader();
        reader.onload = function(ev) {
            preview.src = ev.target.result;
            preview.classList.add('visible');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush

