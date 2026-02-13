<?php

namespace App\Providers;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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
        // Define a simple gate for admin users
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        View::composer('components.elus-header', function ($view) {
            $user = Auth::user();
            $unreadCount = 0;

            if ($user && ($user->isElu() || $user->isAdmin())) {
                $unreadCount = Message::query()
                    ->whereNull('read_at')
                    ->where('user_id', '<>', $user->id)
                    ->whereHas('conversation.users', fn ($query) => $query->where('users.id', $user->id))
                    ->count();
            }

            $view->with('collabUnreadCount', $unreadCount);
        });
    }
}
