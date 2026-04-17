<?php

namespace App\Http\Controllers;

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

        // Get most recent message per conversation partner
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function ($msg) use ($userId) {
                return $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
            })
            ->map(function ($msgs, $partnerId) use ($userId) {
                $latest  = $msgs->first();
                $partner = User::find($partnerId);
                $unread  = $msgs->where('receiver_id', $userId)->whereNull('read_at')->count();
                return (object) [
                    'partner'  => $partner,
                    'latest'   => $latest,
                    'unread'   => $unread,
                ];
            })
            ->values();

        return view('messages.index', compact('conversations'));
    }

    /**
     * Show conversation with a specific user.
     */
    public function conversation(User $user)
    {
        $me = auth()->user();

        if (!$user->allow_dms) {
            return back()->with('error', 'This user has disabled direct messages.');
        }

        $messages = Message::where(function ($q) use ($me, $user) {
                $q->where('sender_id', $me->id)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($me, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $me->id);
            })
            ->orderBy('created_at')
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
            return back()->with('error', 'You cannot message yourself.');
        }

        if (!$user->allow_dms) {
            return back()->with('error', 'This user has disabled direct messages.');
        }

        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        Message::create([
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

        return back()->with('success', 'Message sent.');
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