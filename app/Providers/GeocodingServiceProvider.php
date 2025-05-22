<?php

namespace App\Providers;

use App\Services\GeocodingService;
use Illuminate\Support\ServiceProvider;

class GeocodingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(GeocodingService::class, function ($app) {
            return new GeocodingService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 