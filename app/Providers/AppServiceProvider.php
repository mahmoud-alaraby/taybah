<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Musonza\Chat\Models\MessageNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['admin.layouts.app', 'employee.layouts.app'], function ($view) {
            $user = auth('admin')->user() ?? auth('employee')->user();
            $unreadChatCount = 0;
            $chatConversationsUrl = null;
            if ($user) {
                $unreadChatCount = (new MessageNotification)->unReadNotifications($user)->count();
                $chatConversationsUrl = auth('admin')->check()
                    ? route('admin.conversations.index')
                    : route('employee.conversations.index');
            }
            $view->with(compact('unreadChatCount', 'chatConversationsUrl'));
        });
    }
}
