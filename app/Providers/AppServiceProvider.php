<?php

namespace App\Providers;

use App\Repositories\StoreRepository;
use App\Repositories\StoreRepositoryImpl;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepositoryImpl;
use App\Services\UserService;
use App\Services\UserServiceImpl;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, UserRepositoryImpl::class);
        $this->app->bind(UserService::class, UserServiceImpl::class);
        $this->app->bind(StoreRepository::class, StoreRepositoryImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
