<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // You can register other services here if needed
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // If you have any specific boot logic, you can add it here
    }
}
