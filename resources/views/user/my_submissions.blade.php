@extends('layouts.app')

@section('title', 'My Submissions')

@section('content')
    <div class="card">
        <h1>My Book Submissions</h1>
        <p class="muted">Track the status of books you have submitted for review.</p>
        <a href="{{ route('user.publish') }}">Submit a new book</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr><th>Cover</th><th>Title / Author</th><th>Genre</th><th>Status</th><th>Submitted</th><th>Reviewed</th><th>Note</th></tr>
            </thead>
            <tbody>
                @forelse($submissions as $sub)
                    <tr>
                        <td><img src="{{ $sub->coverUrl() }}" style="width:40px;height:55px;object-fit:cover;border-radius:4px;"></td>
                        <td>{{ $sub->title }}<br><span class="muted">{{ $sub->author }}</span></td>
                        <td>{{ $sub->genre }}</td>
                        <td>
                            @if($sub->isPending())
                                <span class="badge badge-yellow">Pending Review</span>
                            @elseif($sub->isApproved())
                                <span class="badge badge-green">Approved</span>
                            @else
                                <span class="badge badge-red">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $sub->created_at->format('M d, Y') }}</td>
                        <td>{{ $sub->reviewed_at?->format('M d, Y') ?? '—' }}</td>
                        <td style="font-size:13px; color:#6b7280; max-width:200px;">{{ $sub->rejection_reason ?? '' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No submissions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection