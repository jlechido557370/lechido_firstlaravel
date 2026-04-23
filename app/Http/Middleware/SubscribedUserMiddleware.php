<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allows access to: admin, staff, subscribed_user
 * Blocks: regular user (role = 'user')
 */
class SubscribedUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->isSubscribed() && !$user->isAdminOrStaff()) {
            abort(403, 'This area requires a subscription.');
        }

        return $next($request);
    }
}