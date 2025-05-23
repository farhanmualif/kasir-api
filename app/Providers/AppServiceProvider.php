<?php

namespace App\Providers;

use App\Repositories\CategoryRepository;
use App\Repositories\CategoryRepositoryImpl;
use App\Repositories\DetailTransactionRepository;
use App\Repositories\DetailTransactionRepositoryImpl;
use App\Repositories\DiscountRepository;
use App\Repositories\DiscountRepositoryImpl;
use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryImpl;
use App\Repositories\PurchaseReportRepository;
use App\Repositories\PurchaseReportRepositoryImpl;
use App\Repositories\PurchasingRepository;
use App\Repositories\PurchasingRepositoryImpl;
use App\Repositories\SalesReportRepository;
use App\Repositories\SalesReportRepositoryImpl;
use App\Repositories\StoreRepository;
use App\Repositories\StoreRepositoryImpl;
use App\Repositories\TransactionRepository;
use App\Repositories\TransactionRepositoryImpl;
use App\Repositories\UserRepository;
use App\Services\CategoryServiceImpl;
use App\Services\ProductServiceImpl;
use App\Services\PurchaseReportService;
use App\Services\PurchaseReportServiceImpl;
use App\Services\SalesReportServiceImpl;
use App\Services\TransactionServiceImpl;
use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepositoryImpl;
use App\Services\CategoryService;
use App\Services\DiscountService;
use App\Services\DiscountServiceImpl;
use App\Services\FileService;
use App\Services\FileServiceImpl;
use App\Services\ProductService;
use App\Services\PurchasingService;
use App\Services\SalesReportService;
use App\Services\StoreService;
use App\Services\UserService;
use App\Services\UserServiceImpl;
use App\Services\StoreServiceImpl;
use App\Services\TransactionService;
use App\Services\PurchasingServiceImpl;

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
        $this->app->bind(StoreService::class, StoreServiceImpl::class);

        $this->app->bind(ProductRepository::class, ProductRepositoryImpl::class);
        $this->app->bind(ProductService::class, ProductServiceImpl::class);

        $this->app->bind(PurchasingRepository::class, PurchasingRepositoryImpl::class);
        $this->app->bind(PurchasingService::class, PurchasingRepositoryImpl::class);

        $this->app->bind(FileService::class, FileServiceImpl::class);

        $this->app->bind(CategoryRepository::class, CategoryRepositoryImpl::class);
        $this->app->bind(CategoryService::class, CategoryServiceImpl::class);

        $this->app->bind(TransactionRepository::class, TransactionRepositoryImpl::class);
        $this->app->bind(TransactionService::class, TransactionServiceImpl::class);

        $this->app->bind(DetailTransactionRepository::class, DetailTransactionRepositoryImpl::class);

        $this->app->bind(SalesReportRepository::class, SalesReportRepositoryImpl::class);
        $this->app->bind(SalesReportService::class, SalesReportServiceImpl::class);

        $this->app->bind(PurchaseReportService::class, PurchaseReportServiceImpl::class);
        $this->app->bind(PurchaseReportRepository::class, PurchaseReportRepositoryImpl::class);

        $this->app->bind(PurchasingRepository::class, PurchasingRepositoryImpl::class);
        $this->app->bind(PurchasingService::class, PurchasingServiceImpl::class);

        $this->app->bind(DiscountRepository::class, DiscountRepositoryImpl::class);
        $this->app->bind(DiscountService::class, DiscountServiceImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
