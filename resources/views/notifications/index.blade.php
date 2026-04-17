@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Notifications</h1>
            @if($notifications->where('read_at', null)->count() > 0)
                <form method="POST" action="{{ route('notifications.read_all') }}">
                    @csrf
                    <button type="submit" style="width:auto; padding:6px 12px;">Mark all as read</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        @forelse($notifications as $notification)
            <div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; {{ $notification->read_at ? 'opacity:0.6;' : '' }}">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
                    <div>
                        <span style="font-size:12px; font-weight:bold; text-transform:uppercase; color:
                            @if($notification->type === 'overdue') #dc2626
                            @elseif($notification->type === 'due_soon') #92400e
                            @elseif($notification->type === 'payment_confirmed') #15803d
                            @else #6b7280
                            @endif
                        ;">
                            {{ str_replace('_', ' ', $notification->type) }}
                        </span>
                        <p style="margin: 4px 0 0;">{{ $notification->message }}</p>
                    </div>
                    <div style="white-space:nowrap; font-size:12px; color:#6b7280;">
                        {{ $notification->created_at->format('M d, Y H:i') }}
                        @if(!$notification->read_at)
                            <span class="badge badge-red" style="font-size:10px; margin-left:4px;">New</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="muted">No notifications.</p>
        @endforelse
    </div>
@endsection