@extends('layouts.app')

@section('title', 'Conversation with ' . $user->displayName())

@section('content')
    @php
        $lastMsgId = $messages->isEmpty() ? 0 : $messages->last()->id;
    @endphp

    <div class="card" style="display:flex; align-items:center; gap:14px; padding:18px 24px;">
        <a href="{{ route('messages.index') }}" class="btn-outline" style="padding:6px 12px; font-size:13px;">&larr; Back</a>
        <img src="{{ $user->avatarUrl() }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover; border:1.5px solid var(--border);">
        <div>
            <strong style="font-size:15px;"><a href="{{ route('user.public_profile', $user->id) }}">{{ $user->displayName() }}</a></strong>
            <div class="muted" style="font-size:12px;">Member since {{ $user->created_at->format('M Y') }}</div>
        </div>
    </div>

    <div class="card" style="max-height:65vh; min-height:300px; overflow-y:auto; padding:20px 24px;" id="msg-thread">
        @forelse($messages as $msg)
            @php $isMine = $msg->sender_id === auth()->id(); @endphp
            <div data-id="{{ $msg->id }}" data-mine="{{ $isMine ? '1' : '0' }}" style="display:flex; justify-content:{{ $isMine ? 'flex-end' : 'flex-start' }}; margin-bottom:14px;">
                <div class="msg-bubble-inner {{ $isMine ? 'msg-bubble-mine' : 'msg-bubble-theirs' }}">
                    <div style="white-space:pre-wrap; word-wrap:break-word;">{{ $msg->body }}</div>
                    <div class="msg-time">{{ $msg->created_at->format('M d, H:i') }}</div>
                </div>
            </div>
        @empty
            <div class="empty-state" id="no-msg-placeholder">
                <div class="empty-state-icon">✉</div>
                <div class="empty-state-title">No messages yet</div>
                <div class="empty-state-desc">Send the first message to start the conversation.</div>
            </div>
        @endforelse
    </div>

    @if($user->allow_dms)
        <div class="card" style="padding:20px 24px;">
            <form id="msg-form" method="POST" action="{{ route('messages.send', $user->id) }}">
                @csrf
                <div style="display:flex; gap:10px; align-items:flex-end;">
                    <textarea name="body" id="msg-body" rows="3" placeholder="Write a message..." required style="flex:1; resize:vertical; font-size:15px; padding:12px 14px;"></textarea>
                    <button type="submit" id="msg-send-btn" style="width:auto; padding:10px 20px; font-size:14px;">Send</button>
                </div>
                <div id="msg-error" style="color:#b91c1c; font-size:13px; margin-top:6px; display:none;"></div>
            </form>
        </div>
    @else
        <div class="card">
            <p class="muted">This user has disabled direct messages.</p>
        </div>
    @endif
@endsection

@push('scripts')
<style>
    #msg-thread::-webkit-scrollbar { width: 5px; }
    #msg-thread::-webkit-scrollbar-track { background: transparent; }
    #msg-thread::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
</style>
<script>
    (function() {
        var thread = document.getElementById('msg-thread');
        var form = document.getElementById('msg-form');
        var bodyInput = document.getElementById('msg-body');
        var sendBtn = document.getElementById('msg-send-btn');
        var errorDiv = document.getElementById('msg-error');

        // Track latest message ID for polling
        var latestId = {{ $lastMsgId }};
        var pollUrl = '{{ route("messages.poll", $user->id) }}';
        var pollTimeout = null;
        var isPageVisible = true;

        if (thread) thread.scrollTop = thread.scrollHeight;

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function appendMessage(msg, isMine) {
            // Skip if already present
            if (msg.id && thread.querySelector('[data-id="' + msg.id + '"]')) {
                return;
            }

            // Remove placeholder if present
            var placeholder = document.getElementById('no-msg-placeholder');
            if (placeholder) placeholder.remove();

            var bubble = document.createElement('div');
            var justify = isMine ? 'flex-end' : 'flex-start';
            var bg = isMine ? '#111827' : '#f3f4f6';
            var color = isMine ? 'white' : '#111827';
            bubble.setAttribute('data-id', msg.id);
            bubble.style.cssText = 'display:flex; justify-content:' + justify + '; margin-bottom:14px;';
            bubble.innerHTML =
                '<div style="max-width:72%; background:' + bg + '; color:' + color + '; padding:12px 16px; border-radius:14px; line-height:1.5; font-size:15px;">' +
                    '<div style="white-space:pre-wrap; word-wrap:break-word;">' + escapeHtml(msg.body) + '</div>' +
                    '<div style="font-size:11px; opacity:0.55; margin-top:6px; text-align:right;">' + escapeHtml(msg.created_at) + '</div>' +
                '</div>';
            thread.appendChild(bubble);
            thread.scrollTop = thread.scrollHeight;
        }

        function doPoll() {
            if (!isPageVisible) {
                pollTimeout = setTimeout(doPoll, 3000);
                return;
            }
            var url = pollUrl + (pollUrl.indexOf('?') > -1 ? '&' : '?') + 'after_id=' + encodeURIComponent(latestId);
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.messages && res.messages.length > 0) {
                    res.messages.forEach(function(msg) {
                        appendMessage(msg, msg.is_mine);
                        if (msg.id > latestId) latestId = msg.id;
                    });
                }
                if (res.latest_id > latestId) latestId = res.latest_id;
                pollTimeout = setTimeout(doPoll, 3000);
            })
            .catch(function() {
                pollTimeout = setTimeout(doPoll, 3000);
            });
        }

        // Start first poll; subsequent polls scheduled after each request finishes
        pollTimeout = setTimeout(doPoll, 3000);

        // Pause polling when tab is hidden
        document.addEventListener('visibilitychange', function() {
            isPageVisible = !document.hidden;
        });

        // Send form via AJAX
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var body = bodyInput.value.trim();
            if (!body) return;

            sendBtn.classList.add('btn-loading');
            errorDiv.style.display = 'none';

            var data = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: data
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                sendBtn.classList.remove('btn-loading');
                if (res.error) {
                    errorDiv.textContent = res.error;
                    errorDiv.style.display = 'block';
                    return;
                }
                if (res.success && res.message) {
                    appendMessage(res.message, true);
                    if (res.message.id > latestId) latestId = res.message.id;
                    bodyInput.value = '';
                    bodyInput.focus();
                }
            })
            .catch(function() {
                sendBtn.classList.remove('btn-loading');
                errorDiv.textContent = 'Failed to send. Please try again.';
                errorDiv.style.display = 'block';
            });
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            if (pollTimeout) clearTimeout(pollTimeout);
        });
    })();
</script>
@endpush
