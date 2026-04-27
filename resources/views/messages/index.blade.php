@extends('layouts.app')

@section('title', 'Messages')

@section('content')
    <div class="section-header">
        <div>
            <h1 style="margin-bottom:4px;">Messages</h1>
            <p class="muted" style="font-size:14px;">Your conversations with other readers.</p>
        </div>
    </div>

    <div class="card" style="padding:0; overflow:hidden;">
        @forelse($conversations as $convo)
            @if(!$convo->partner)
                @continue
            @endif
            <a href="{{ route('messages.conversation', $convo->partner->id) }}" class="msg-convo-link {{ $convo->unread > 0 ? 'msg-convo-unread' : '' }}">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="position:relative; flex-shrink:0;">
                        <img src="{{ $convo->partner->avatarUrl() }}" style="width:50px;height:50px;border-radius:50%;object-fit:cover; border:1.5px solid var(--border);">
                        @if($convo->unread > 0)
                            <span style="position:absolute; top:-2px; right:-2px; background:#dc2626; color:#fff; font-size:10px; font-weight:700; min-width:18px; height:18px; display:flex; align-items:center; justify-content:center; border-radius:50%; border:2px solid var(--white);">{{ $convo->unread }}</span>
                        @endif
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:4px;">
                            <strong style="font-size:15px; color:var(--black);">{{ $convo->partner->displayName() }}</strong>
                            <span class="muted" style="font-size:12px; white-space:nowrap; font-family:var(--font-mono);">{{ $convo->latest->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="color:var(--muted); font-size:14px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; line-height:1.4;">
                            @if($convo->latest->sender_id === auth()->id())
                                <span style="color:var(--black); font-weight:500;">You:</span>
                            @endif
                            {{ $convo->latest->body }}
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">💬</div>
                <div class="empty-state-title">No conversations yet</div>
                <div class="empty-state-desc">Go to a user's profile and click Message to start a conversation.</div>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
<style>
.msg-convo-link {
    display: block;
    padding: 18px 24px;
    border-bottom: 1px solid var(--mid);
    text-decoration: none;
    color: var(--black);
    transition: background .12s, padding-left .15s;
}
.msg-convo-link:last-child { border-bottom: none; }
.msg-convo-link:hover { background: var(--off); padding-left: 28px; }
.msg-convo-unread { background: var(--accent-soft); border-left: 3px solid var(--accent); padding-left: 21px; }
.msg-convo-unread:hover { padding-left: 25px; }
</style>
@endpush
