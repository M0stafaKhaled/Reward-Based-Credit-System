<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(\App\Repositories\CreditPackageRepositoryInterface::class, \App\Repositories\CreditPackageRepository::class);
        $this->app->bind(\App\Repositories\PurchaseRepositoryInterface::class, \App\Repositories\PurchaseRepository::class);
        $this->app->bind(\App\Repositories\RewardPointRepositoryInterface::class, \App\Repositories\RewardPointRepository::class);
        $this->app->bind(\App\Repositories\ProductRepositoryInterface::class, \App\Repositories\ProductRepository::class);
        $this->app->bind(\App\Repositories\OfferPoolRepositoryInterface::class, \App\Repositories\OfferPoolRepository::class);
    }

    public function boot()
    {
        //
    }
} 