<?php

namespace App\Providers;

use App\Services\EventService;
use App\Services\EventServiceInterface;
use Illuminate\Support\ServiceProvider;

/**
 * The application service provider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EventService::class, function ($app) {
            return new EventService();
        });
        $this->app->bind(EventServiceInterface::class, EventService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
