<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;

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
         if (app()->environment('production')) {
            URL::forceScheme('https');
         }
        // Point password reset emails to the Vue frontend route
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('app.url') . '/reset-password/' . $token . '?email=' . urlencode($user->email);
        });
    }
}
