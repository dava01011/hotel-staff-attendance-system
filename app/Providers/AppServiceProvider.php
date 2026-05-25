<?php

namespace App\Providers;

use App\Models\Notifikasi;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

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
        view()->composer('*', function ($view) {
            if (Auth::check()) {
                $notifUnread = Notifikasi::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->latest()
                    ->take(5)
                    ->get();

                $notifCount = $notifUnread->count();

                $view->with(compact('notifUnread', 'notifCount'));
            }
        });

        User::observe(UserObserver::class);
    }
}
