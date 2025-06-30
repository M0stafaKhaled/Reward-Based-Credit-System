<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Purchase;
use App\Observers\CreditLogObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Services\CacheService::class, function ($app) {
            return new \App\Services\CacheService();
        });
        $this->app->bind(\App\Services\ProductService::class, function ($app) {
            return new \App\Services\ProductService(
                $app->make(\App\Repositories\ProductRepositoryInterface::class),
                $app->make(\App\Services\CacheService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Purchase::observe(CreditLogObserver::class);
    }
}
