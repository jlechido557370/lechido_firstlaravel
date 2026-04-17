<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\UserNotification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $userId = auth()->id();

                $unreadNotificationCount = UserNotification::where('user_id', $userId)
                    ->whereNull('read_at')->count();

                $unreadMessageCount = Message::where('receiver_id', $userId)
                    ->whereNull('read_at')->count();

                $view->with('unreadNotificationCount', $unreadNotificationCount);
                $view->with('unreadMessageCount', $unreadMessageCount);
            } else {
                $view->with('unreadNotificationCount', 0);
                $view->with('unreadMessageCount', 0);
            }
        });
    }
}