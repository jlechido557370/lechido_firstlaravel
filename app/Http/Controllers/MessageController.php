<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Message;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * List all conversations for the logged-in user.
     */
    public function index()
    {
        $userId = auth()->id();

        // Efficiently get the latest message ID per conversation partner
        $latestMessageIds = DB::table('messages')
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->selectRaw('MAX(id) as max_id')
            ->groupByRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END', [$userId])
            ->pluck('max_id');

        $latestMessages = Message::whereIn('id', $latestMessageIds)
            ->orderByDesc('created_at')
            ->get();

        $partnerIds = $latestMessages->map(function ($msg) use ($userId) {
            return $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
        })->unique()->values();

        $partners = User::whereIn('id', $partnerIds)->get()->keyBy('id');

        // Get unread counts per partner in one query
        $unreadCounts = Message::where('receiver_id', $userId)
            ->whereNull('read_at')
            ->whereIn('sender_id', $partnerIds)
            ->selectRaw('sender_id, COUNT(*) as count')
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id');

        $conversations = $latestMessages->map(function ($msg) use ($userId, $partners, $unreadCounts) {
            $partnerId = $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
            return (object) [
                'partner' => $partners->get($partnerId),
                'latest'  => $msg,
                'unread'  => $unreadCounts->get($partnerId, 0),
            ];
        });

        return view('messages.index', compact('conversations'));
    }

    /**
     * Check if either user has blocked the other.
     */
    private function isBlocked(User $a, User $b): bool
    {
        return Block::where(function ($q) use ($a, $b) {
                $q->where('blocker_id', $a->id)->where('blocked_id', $b->id);
            })
            ->orWhere(function ($q) use ($a, $b) {
                $q->where('blocker_id', $b->id)->where('blocked_id', $a->id);
            })
            ->exists();
    }

    /**
     * Return a block error response.
     */
    private function blockError(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['error' => 'You cannot message this user.'], 403);
        }
        return back()->with('error', 'You cannot message this user.');
    }

    /**
     * Show conversation with a specific user.
     */
    public function conversation(User $user)
    {
        $me = auth()->user();

        if ($this->isBlocked($me, $user)) {
            return back()->with('error', 'You cannot message this user.');
        }

        if (!$user->allow_dms) {
            return back()->with('error', 'This user has disabled direct messages.');
        }

        $messages = Message::where(function ($q) use ($me, $user) {
                $q->where('sender_id', $me->id)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($me, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $me->id);
            })
            ->orderBy('id')
            ->get();

        // Mark received messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $me->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.conversation', compact('user', 'messages'));
    }

    /**
     * Send a message.
     */
    public function send(Request $request, User $user)
    {
        $me = auth()->user();

        if ($me->id === $user->id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'You cannot message yourself.'], 422);
            }
            return back()->with('error', 'You cannot message yourself.');
        }

        if ($this->isBlocked($me, $user)) {
            return $this->blockError($request);
        }

        if (!$user->allow_dms) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'This user has disabled direct messages.'], 422);
            }
            return back()->with('error', 'This user has disabled direct messages.');
        }

        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'sender_id'   => $me->id,
            'receiver_id' => $user->id,
            'body'        => $request->body,
        ]);

        // Create notification for receiver
        UserNotification::create([
            'user_id' => $user->id,
            'type'    => 'new_message',
            'message' => $me->displayName() . ' sent you a message.',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id'          => $message->id,
                    'body'        => $message->body,
                    'created_at'  => $message->created_at->format('M d, H:i'),
                    'is_mine'     => true,
                ],
            ]);
        }

        return back()->with('success', 'Message sent.');
    }

    /**
     * AJAX — poll for new messages in a conversation.
     */
    public function poll(Request $request, User $user)
    {
        $me = auth()->user();

        if ($this->isBlocked($me, $user)) {
            return response()->json(['error' => 'You cannot message this user.'], 403);
        }

        if (!$user->allow_dms) {
            return response()->json(['error' => 'This user has disabled direct messages.'], 403);
        }

        $afterId = $request->input('after_id', 0);

        $messages = Message::where(function ($q) use ($me, $user) {
                $q->where('sender_id', $me->id)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($me, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $me->id);
            })
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->map(function ($msg) use ($me) {
                return [
                    'id'          => $msg->id,
                    'body'        => $msg->body,
                    'created_at'  => $msg->created_at->format('M d, H:i'),
                    'is_mine'     => $msg->sender_id === $me->id,
                ];
            });

        // Mark received messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $me->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages,
            'latest_id' => $messages->isEmpty() ? $afterId : $messages->last()['id'],
        ]);
    }

    /**
     * AJAX — return recent conversations JSON for the dropdown.
     */
    public function recentJson()
    {
        $userId = auth()->id();

        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function ($msg) use ($userId) {
                return $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
            })
            ->take(5)
            ->map(function ($msgs, $partnerId) use ($userId) {
                $latest  = $msgs->first();
                $partner = User::find($partnerId);
                $unread  = $msgs->where('receiver_id', $userId)->whereNull('read_at')->count();
                return [
                    'partner_id'   => $partnerId,
                    'partner_name' => $partner?->displayName() ?? 'Deleted User',
                    'avatar'       => $partner?->avatarUrl() ?? '',
                    'latest_body'  => mb_strimwidth($latest->body, 0, 50, '...'),
                    'unread'       => $unread,
                    'url'          => route('messages.conversation', $partnerId),
                ];
            })
            ->values();

        $totalUnread = Message::where('receiver_id', $userId)->whereNull('read_at')->count();

        return response()->json([
            'conversations' => $conversations,
            'total_unread'  => $totalUnread,
        ]);
    }

    /**
     * AJAX — return recent notifications JSON for the dropdown.
     */
    public function notificationsJson()
    {
        $userId        = auth()->id();
        $notifications = \App\Models\UserNotification::where('user_id', $userId)
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($n) => [
                'id'      => $n->id,
                'type'    => $n->type,
                'message' => $n->message,
                'read'    => !is_null($n->read_at),
                'time'    => $n->created_at->diffForHumans(),
            ]);

        $unread = \App\Models\UserNotification::where('user_id', $userId)
            ->whereNull('read_at')->count();

        return response()->json([
            'notifications' => $notifications,
            'unread'        => $unread,
        ]);
    }
}

