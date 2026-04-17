@extends('layouts.app')

@section('title', 'Conversation with ' . $user->displayName())

@section('content')
    <div class="card" style="display:flex; align-items:center; gap:12px;">
        <a href="{{ url()->previous() }}">&larr; Back</a>
        <img src="{{ $user->avatarUrl() }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
        <div>
            <strong><a href="{{ route('user.public_profile', $user->id) }}">{{ $user->displayName() }}</a></strong>
            <div class="muted" style="font-size:12px;">Member since {{ $user->created_at->format('M Y') }}</div>
        </div>
    </div>

    <div class="card" style="max-height:500px; overflow-y:auto;" id="msg-thread">
        @forelse($messages as $msg)
            @php $isMine = $msg->sender_id === auth()->id(); @endphp
            <div style="display:flex; justify-content:{{ $isMine ? 'flex-end' : 'flex-start' }}; margin-bottom:12px;">
                <div style="max-width:70%; background:{{ $isMine ? '#111827' : '#f3f4f6' }}; color:{{ $isMine ? 'white' : '#111827' }}; padding:10px 14px; border-radius:12px; line-height:1.4;">
                    {{ $msg->body }}
                    <div style="font-size:11px; opacity:0.6; margin-top:4px; text-align:right;">
                        {{ $msg->created_at->format('M d, H:i') }}
                    </div>
                </div>
            </div>
        @empty
            <p class="muted" style="text-align:center; padding:20px 0;">No messages yet. Send the first one.</p>
        @endforelse
    </div>

    @if($user->allow_dms)
        <div class="card">
            <form method="POST" action="{{ route('messages.send', $user->id) }}">
                @csrf
                <div style="display:flex; gap:8px;">
                    <textarea name="body" rows="2" placeholder="Write a message..." required style="flex:1; resize:vertical;">{{ old('body') }}</textarea>
                    <button type="submit" style="width:auto; padding:8px 16px; align-self:flex-end;">Send</button>
                </div>
                @error('body')<div style="color:#b91c1c; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
            </form>
        </div>
    @else
        <div class="card">
            <p class="muted">This user has disabled direct messages.</p>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    // Scroll to bottom of message thread
    var thread = document.getElementById('msg-thread');
    if (thread) thread.scrollTop = thread.scrollHeight;
</script>
@endpush