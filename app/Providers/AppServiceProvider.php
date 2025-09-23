<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
 
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            $authUser = Auth::user();

            $unreadNotificationsCount = 0;
            if ($authUser) {
                $unreadNotificationsCount = Notification::where('user_id', $authUser->id)
                    ->where('is_read', false)
                    ->count();
            }

            $view->with([
                'authUser' => $authUser,
                'unreadNotificationsCount' => $unreadNotificationsCount,
            ]);
        });
    }
}
