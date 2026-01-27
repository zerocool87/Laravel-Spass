<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
            return method_exists($user, 'isAdmin') && $user->isAdmin();
        });
    }
}
