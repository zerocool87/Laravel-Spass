<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        View::composer('components.elus-header', function ($view) {
            $user = Auth::user();
            $unreadCount = 0;

            if ($user && ($user->isElu() || $user->isAdmin())) {
                $unreadCount = Message::query()
                    ->where('user_id', '<>', $user->id)
                    ->whereHas('conversation.users', fn ($query) => $query->where('users.id', $user->id))
                    ->whereDoesntHave('readBy', fn ($query) => $query->where('user_id', $user->id))
                    ->count();
            }

            $view->with('collabUnreadCount', $unreadCount);
        });
    }
}
