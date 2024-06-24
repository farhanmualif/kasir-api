<?php

namespace App\Providers;

use App\Repositories\StoreRepository;
use App\Repositories\StoreRepositoryImpl;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryImpl;
use App\Services\UserServices;
use App\Services\UserServicesImpl;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(UserRepository::class, UserRepositoryImpl::class);
        $this->app->bind(UserServices::class, UserServicesImpl::class);
        $this->app->bind(StoreRepository::class, StoreRepositoryImpl::class);
    }
}
