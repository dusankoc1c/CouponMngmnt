<?php

namespace App\Providers;

use App\Repositories\Contracts\BundleRepositoryInterface;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\StoreRepositoryInterface;
use App\Repositories\EloquentBundleRepo;
use App\Repositories\EloquentCouponRepo;
use App\Repositories\EloquentStoreRepo;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CouponRepositoryInterface::class, EloquentCouponRepo::class);
        $this->app->bind(BundleRepositoryInterface::class, EloquentBundleRepo::class);
        $this->app->bind(StoreRepositoryInterface::class, EloquentStoreRepo::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
