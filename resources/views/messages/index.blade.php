@extends('layouts.app')

@section('title', 'Messages')

@section('content')
    <div class="card">
        <h1>Messages</h1>
    </div>

    <div class="card">
        @forelse($conversations as $convo)
            <a href="{{ route('messages.conversation', $convo->partner->id) }}" style="display:block; padding:12px 0; border-bottom:1px solid #f3f4f6; text-decoration:none; color:#111827;" class="{{ $convo->unread > 0 ? 'unread-convo' : '' }}">
                <div style="display:flex; align-items:center; gap:12px;">
                    <img src="{{ $convo->partner->avatarUrl() }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; justify-content:space-between; gap:8px;">
                            <strong>{{ $convo->partner->displayName() }}</strong>
                            <span class="muted" style="font-size:12px; white-space:nowrap;">{{ $convo->latest->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="color:#6b7280; font-size:13px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            @if($convo->latest->sender_id === auth()->id())
                                You: 
                            @endif
                            {{ $convo->latest->body }}
                        </div>
                    </div>
                    @if($convo->unread > 0)
                        <span class="badge badge-red" style="flex-shrink:0;">{{ $convo->unread }}</span>
                    @endif
                </div>
            </a>
        @empty
            <p class="muted" style="padding:16px 0;">No conversations yet. Go to a user's profile and click Message to start a conversation.</p>
        @endforelse
    </div>
@endsection