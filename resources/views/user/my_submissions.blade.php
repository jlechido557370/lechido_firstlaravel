@extends('layouts.app')

@section('title', 'My Submissions')

@section('content')
    <div class="card" style="padding:28px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <h1 style="font-size:24px; font-weight:600; margin-bottom:4px;">My Submissions</h1>
                <p class="muted">Books you have submitted for review</p>
            </div>
            <a href="{{ route('user.publish') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; background:var(--black); color:var(--white); border-radius:8px; font-size:14px; font-weight:500; text-decoration:none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Submit New Book
            </a>
        </div>
    </div>

    @if($submissions->isEmpty())
        <div class="card" style="padding:48px 28px; text-align:center;">
            <div style="font-size:36px; margin-bottom:12px;">📚</div>
            <h2 style="font-size:18px; font-weight:600; margin-bottom:8px; color:var(--black);">No submissions yet</h2>
            <p class="muted" style="max-width:400px; margin:0 auto 16px;">Submit your own books to share with the library community. All submissions are reviewed by staff before being published.</p>
            <a href="{{ route('user.publish') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; background:var(--black); color:var(--white); border-radius:8px; font-size:14px; font-weight:500; text-decoration:none;">
                Submit a Book
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    @else
        <div class="card" style="padding:0; overflow:hidden;">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left:24px;">Book</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th style="padding-right:24px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $sub)
                        <tr>
                            <td style="padding-left:24px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <img src="{{ $sub->coverUrl() }}" style="width:40px; height:55px; object-fit:cover; border-radius:5px; border:1px solid var(--border); flex-shrink:0; background:var(--mid);">
                                    <div>
                                        <strong style="font-size:15px; color:var(--black);">{{ $sub->title }}</strong>
                                        <div class="muted" style="font-size:12px; margin-top:2px;">{{ $sub->author }} &bull; {{ $sub->genre }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($sub->isPending())
                                    <span class="badge badge-yellow">Pending Review</span>
                                @elseif($sub->isApproved())
                                    <span class="badge badge-green">Approved</span>
                                @else
                                    <span class="badge badge-red">Rejected</span>
                                @endif
                            </td>
                            <td style="color:var(--muted); font-size:12px; font-family:var(--font-mono); white-space:nowrap;">{{ $sub->created_at->format('M d, Y') }}</td>
                            <td style="padding-right:24px;">
                                @if($sub->isPending())
                                    <form method="POST" action="{{ route('user.submissions.cancel', $sub->id) }}" style="margin:0; display:inline;"
                                          onsubmit="return confirm('Cancel this submission?')">
                                        @csrf
                                        <button type="submit" class="btn-outline" style="padding:5px 12px; font-size:12px;">Cancel</button>
                                    </form>
                                @elseif($sub->isApproved())
                                    <a href="{{ route('books.show', $sub->id) }}" style="font-size:13px;">View Book</a>
                                @elseif($sub->rejection_reason)
                                    <span class="muted" style="font-size:12px; max-width:160px; display:block;">{{ $sub->rejection_reason }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

