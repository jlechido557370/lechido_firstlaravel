@extends('layouts.app')
@section('title', 'Publish a Book')
@section('content')
@php
    $user = auth()->user();
    $isSubscribed = $user->isSubscribed();
    $todayCount = \App\Models\UserBook::where('user_id', $user->id)->whereDate('created_at', today())->count();
    $totalCount = \App\Models\UserBook::where('user_id', $user->id)->count();
    $atLimit = $isSubscribed ? ($totalCount >= 50) : ($todayCount >= 2);

    $allGenres = ['Action','Adventure','Biography','Business','Children','Classic','Drama','Fantasy','Fiction',
                  'History','Horror','Mystery','Philosophy','Programming','Romance','Science','Science Fiction',
                  'Self-Help','Sports','Thriller','Travel','Young Adult'];
@endphp

<div class="card">
    <h1>Publish a Book</h1>
    @if($isSubscribed)
        <p class="muted">Subscriber: <strong>{{ $totalCount }} / 50</strong> total submissions used.</p>
    @else
        <p class="muted">Free users: <strong>{{ $todayCount }} / 2</strong> submissions today. <a href="{{ route('subscription.index') }}">Subscribe</a> to publish up to 50 books total.</p>
    @endif
</div>

@if($atLimit)
    <div class="card">
        <div class="flash error">
            @if($isSubscribed)
                You have reached the 50-book subscriber publish limit.
            @else
                You have reached today's free limit of 2 submissions. <a href="{{ route('subscription.index') }}">Subscribe</a> to publish up to 50 books total.
            @endif
        </div>
    </div>
@else
    <div class="card">
        <form method="POST" action="{{ route('user.publish.store') }}" enctype="multipart/form-data" id="publishForm">
            @csrf

            <div style="display:flex; gap:20px; flex-wrap:wrap; align-items:flex-start; margin-bottom:16px;">
                <div style="flex-shrink:0; text-align:center;">
                    <div id="cover-preview" style="width:128px; height:190px; background:#e5e7eb; border:2px dashed #9ca3af; display:flex; align-items:center; justify-content:center; font-size:12px; color:#6b7280; margin-bottom:8px; overflow:hidden;">
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
                        <input type="text" name="title" id="autoTitle" value="{{ old('title') }}" required placeholder="Book title">
                        @error('title')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                    </div>
                    <div style="margin-bottom:12px;">
                        <label>Author *</label>
                        <input type="text" name="author" id="autoAuthor" value="{{ old('author') }}" required placeholder="Author name">
                        @error('author')<div style="color:#b91c1c; font-size:13px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div>
                            <label>Published Year</label>
                            <input type="number" name="published_year" value="{{ old('published_year') }}" min="1" max="{{ date('Y') }}" placeholder="{{ date('Y') }}">
                        </div>
                        <div>
                            <label>ISBN <span class="muted" style="font-size:12px;">(optional)</span></label>
                            <input type="text" name="isbn" value="{{ old('isbn') }}" placeholder="978-...">
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <label>Genres * <span class="muted" style="font-size:12px;">(select one or more)</span></label>
                <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:8px; padding:10px; border:1px solid #bbb; border-radius:6px;">
                    @foreach($allGenres as $g)
                        <label style="display:flex; align-items:center; gap:4px; cursor:pointer; font-weight:normal; font-size:13px;">
                            <input type="checkbox" name="genres[]" value="{{ $g }}"
                                   {{ in_array($g, (array) old('genres', [])) ? 'checked' : '' }}
                                   id="genreCheckbox" style="width:auto;">
                            {{ $g }}
                        </label>
                    @endforeach
                </div>
                @error('genres')<div style="color:#b91c1c; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:12px;">
                <label>Description</label>
                <textarea name="description" id="autoDescription" rows="5" placeholder="Write a description or synopsis...">{{ old('description') }}</textarea>
            </div>

            <div style="margin-bottom:16px;">
                <label>Read URL <span class="muted" style="font-size:12px;">(optional)</span></label>
                <input type="url" name="read_url" value="{{ old('read_url') }}" placeholder="https://...">
            </div>

            <button type="submit">Submit for Review</button>
        </form>
    </div>
@endif

@if($user->userBooks()->count() > 0)
    <div class="card">
        <h2>My Submissions</h2>
        <table>
            <thead><tr><th>Cover</th><th>Title</th><th>Status</th><th>Submitted</th><th>Note</th></tr></thead>
            <tbody>
                @foreach($user->userBooks()->latest()->take(10)->get() as $sub)
                    <tr>
                        <td><img src="{{ $sub->coverUrl() }}" style="width:40px;height:55px;object-fit:cover;border-radius:4px;"></td>
                        <td>{{ $sub->title }}<br><span class="muted">{{ $sub->author }}</span></td>
                        <td>
                            @if($sub->isPending())<span class="badge badge-yellow">Pending</span>
                            @elseif($sub->isApproved())<span class="badge badge-green">Approved</span>
                            @else<span class="badge badge-red">Rejected</span>@endif
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
            preview.innerHTML = '<img src="' + e.target.result + '" style="width:128px;height:190px;object-fit:cover;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush