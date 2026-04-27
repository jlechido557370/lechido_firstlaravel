@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="section-header">
        <div>
            <h1>Notifications</h1>
            <p class="muted">Stay updated on your library activity.</p>
        </div>
        @if($notifications->where('read_at', null)->count() > 0)
            <form method="POST" action="{{ route('notifications.read_all') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-outline" style="padding:8px 16px; font-size:13px;">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    <div class="card" style="padding:0; overflow:hidden;">
        @forelse($notifications as $notification)
            @php
                $type = $notification->type;
                $icon = match($type) {
                    'overdue' => '⚠',
                    'due_soon' => '⏰',
                    'payment_confirmed' => '✓',
                    'book_approved' => '📚',
                    'book_rejected' => '✕',
                    default => '🔔',
                };
                $labelClass = match($type) {
                    'overdue' => 'overdue',
                    'due_soon' => 'due_soon',
                    'payment_confirmed' => 'payment_confirmed',
                    default => 'default',
                };
                $iconClass = match($type) {
                    'overdue' => 'overdue',
                    'due_soon' => 'due_soon',
                    'payment_confirmed' => 'payment_confirmed',
                    default => 'default',
                };
            @endphp
            <div class="notif-item {{ $notification->read_at ? 'read' : '' }}">
                <div class="notif-icon {{ $iconClass }}">{{ $icon }}</div>
                <div class="notif-body">
                    <div class="notif-type-label {{ $labelClass }}">{{ str_replace('_', ' ', $type) }}</div>
                    <p class="notif-message">{{ $notification->message }}</p>
                    <div class="notif-meta">{{ $notification->created_at->format('M d, Y \a\t H:i') }}</div>
                </div>
                <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px; flex-shrink:0;">
                    <span class="notif-meta">{{ $notification->created_at->diffForHumans() }}</span>
                    @if(!$notification->read_at)
                        <span class="notif-badge-new">New</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">🔔</div>
                <div class="empty-state-title">All caught up</div>
                <div class="empty-state-desc">No notifications to show right now.</div>
            </div>
        @endforelse
    </div>
@endsection
