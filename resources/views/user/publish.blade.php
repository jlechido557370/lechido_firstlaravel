@extends('layouts.app')

@section('title', 'Publish a Book')

@section('content')
    <div class="card">
        <h1>Publish a Book</h1>
        <p class="muted">Submit a book for review. You can submit up to 2 books per day. Staff will review and approve before it goes live.</p>
        @php
            $todayCount = \App\Models\UserBook::where('user_id', auth()->id())->whereDate('created_at', today())->count();
        @endphp
        <p class="muted">Today's submissions: <strong>{{ $todayCount }} / 2</strong></p>
    </div>

    @if($todayCount >= 2)
        <div class="card">
            <div class="flash error">You have reached your daily limit of 2 book submissions. You can submit more tomorrow.</div>
        </div>
    @else
        <div class="card">
            <form method="POST" action="{{ route('user.publish.store') }}" enctype="multipart/form-data">
                @csrf

                <div style="display:flex; gap:20px; flex-wrap:wrap; align-items:flex-start; margin-bottom:16px;">
                    <div style="flex-shrink:0; text-align:center;">
                        <div id="cover-preview" style="width:128px; height:180px; background:#e5e7eb; border:2px dashed #9ca3af; display:flex; align-items:center; justify-content:center; font-size:12px; color:#6b7280; margin-bottom:8px;">
                            No Cover
                        </div>
                        <label style="font-size:13px; cursor:pointer; color:#0b5ed7;">
                            <input type="file" name="cover_image" id="cover_image" accept="image/*" style="display:none;" onchange="previewCover(this)">
                            Upload Cover
                        </label>
                    </div>
                    <div style="flex:1; min-width:240px;">
                        <div style="margin-bottom:12px;">
                            <label>Title *</label>
                            <input type="text" name="title" value="{{ old('title') }}" required placeholder="Book title">
                            @error('title')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                        </div>
                        <div style="margin-bottom:12px;">
                            <label>Author *</label>
                            <input type="text" name="author" value="{{ old('author') }}" required placeholder="Author name">
                            @error('author')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div>
                                <label>Genre *</label>
                                <input type="text" name="genre" value="{{ old('genre') }}" required placeholder="e.g. Fiction">
                                @error('genre')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label>Published Year</label>
                                <input type="number" name="published_year" value="{{ old('published_year') }}" min="1" max="{{ date('Y') }}" placeholder="{{ date('Y') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom:12px;">
                    <label>ISBN <span class="muted" style="font-size:12px;">(optional)</span></label>
                    <input type="text" name="isbn" value="{{ old('isbn') }}" placeholder="978-...">
                    @error('isbn')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:12px;">
                    <label>Description</label>
                    <textarea name="description" rows="5" placeholder="Write a description or synopsis...">{{ old('description') }}</textarea>
                    @error('description')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label>Read URL <span class="muted" style="font-size:12px;">(optional — link to read online)</span></label>
                    <input type="url" name="read_url" value="{{ old('read_url') }}" placeholder="https://...">
                    @error('read_url')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                </div>

                <button type="submit">Submit for Review</button>
            </form>
        </div>
    @endif

    @if(auth()->user()->userBooks()->count() > 0)
        <div class="card">
            <h2>My Submissions</h2>
            <table>
                <thead>
                    <tr><th>Cover</th><th>Title</th><th>Status</th><th>Submitted</th><th>Note</th></tr>
                </thead>
                <tbody>
                    @foreach(auth()->user()->userBooks()->latest()->take(10)->get() as $sub)
                        <tr>
                            <td><img src="{{ $sub->coverUrl() }}" style="width:40px;height:55px;object-fit:cover;border-radius:4px;"></td>
                            <td>{{ $sub->title }}<br><span class="muted">{{ $sub->author }}</span></td>
                            <td>
                                @if($sub->isPending())
                                    <span class="badge badge-yellow">Pending</span>
                                @elseif($sub->isApproved())
                                    <span class="badge badge-green">Approved</span>
                                @else
                                    <span class="badge badge-red">Rejected</span>
                                @endif
                            </td>
                            <td>{{ $sub->created_at->format('M d, Y') }}</td>
                            <td style="font-size:13px; color:#6b7280;">{{ $sub->rejection_reason ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

@push('scripts')
<script>
function previewCover(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('cover-preview');
            preview.innerHTML = '<img src="' + e.target.result + '" style="width:128px;height:180px;object-fit:cover;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush