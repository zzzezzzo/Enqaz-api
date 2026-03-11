<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;


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
        Route::middleware('api')
        ->prefix('api/admin')
        ->group(base_path('routes/api/admin.php'));
        Route::middleware('api')
        ->prefix('api/customer')
        ->group(base_path('routes/api/customer.php'));
        Route::middleware('api')
        ->prefix('api/provider')
        ->group(base_path('routes/api/provider.php'));
    }
}
