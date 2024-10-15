<?php

namespace App\Providers;

use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryImpl;
use App\Repositories\StoreRepository;
use App\Repositories\StoreRepositoryImpl;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryImpl;
use App\Services\StoreServices;
use App\Services\StoreServicesImpl;
use App\Services\UserService;
use App\Services\UserServiceImpl;
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
        $this->app->bind(UserService::class, UserServiceImpl::class);

        $this->app->bind(StoreRepository::class, StoreRepositoryImpl::class);
        $this->app->bind(StoreServices::class, StoreServicesImpl::class);

        $this->app->bind(ProductRepository::class, ProductRepositoryImpl::class);
    }
}
