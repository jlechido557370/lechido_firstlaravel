<?php
namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\User;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function toggle(User $user)
    {
        $me = auth()->user();

        if ($me->id === $user->id) {
            return back()->with('error', 'You cannot block yourself.');
        }

        $existing = Block::where('blocker_id', $me->id)->where('blocked_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'User unblocked.');
        }

        Block::create(['blocker_id' => $me->id, 'blocked_id' => $user->id]);
        return back()->with('success', 'User blocked. They can no longer message you or see your profile activity.');
    }
}