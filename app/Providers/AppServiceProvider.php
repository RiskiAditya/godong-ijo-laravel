<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Share pending bookings count with all admin views
        view()->composer('layouts.admin', function ($view) {
            $pendingBookingsCount = \App\Models\Pemesanan::where('status', 'pending')->count();
            $view->with('pendingBookingsCount', $pendingBookingsCount);
        });
    }
}
