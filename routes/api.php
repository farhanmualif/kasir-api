<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RaportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::get('authenticated', [AuthController::class, 'authenticated']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Resources
    Route::apiResources([
        'products' => ProductController::class,
        'categories' => CategoryController::class,
        'users' => UserController::class,
        'transactions' => TransactionController::class,
        'barcodes' => BarcodeController::class,
    ]);

    // Product related routes
    Route::prefix('products')->group(function () {
        Route::post('purchase/existing', [ProductController::class, 'purchaseProductsExist']);
        Route::post('upload', [ProductController::class, 'uploadImage']);
        Route::get('{barcode}/barcode', [ProductController::class, 'showByBarcode']);
        Route::put('{uuid}/image', [ProductController::class, 'updateImage']);
        Route::get('images/{uuid}', [ProductController::class, 'showImage']);

        // Product categories
        Route::post('{productUuid}/categories', [ProductController::class, 'addCategoriesToProduct']);
        Route::delete('{productUuid}/categories', [ProductController::class, 'removeCategoriesFromProduct']);
    });

    // Category related routes
    Route::prefix('categories')->group(function () {
        Route::get('{categoryName}/products', [ProductController::class, 'showByCategory']);
        Route::put('{uuid}/product', [CategoryController::class, 'updateByProductUuid']);
    });

    // Sales reports
    Route::prefix('reports/sales')->group(function () {
        Route::get('daily/{date}', [RaportController::class, 'getDailySales']);
        Route::get('monthly/{date}', [RaportController::class, 'getMonthlySales']);
        Route::get('yearly/{date}', [RaportController::class, 'getYearlySales']);
    });

    // Purchase reports
    Route::prefix('reports/purchases')->group(function () {
        Route::get('daily/{date}', [RaportController::class, 'getDailyPurchases']);
        Route::get('monthly/{date}', [RaportController::class, 'getMonthlyPurchases']);
        Route::get('yearly/{date}', [RaportController::class, 'getYearlyPurchases']);
    });

    // Transaction invoices
    Route::prefix('transactions')->group(function () {
        Route::get('{noTransaction}/invoice', [TransactionController::class, 'showInvoice']);
        Route::get('{noTransaction}/sales-invoice', [TransactionController::class, 'showSalesInvoice']);
    });

    // Discounts
    Route::prefix('discounts')->group(function () {
        Route::get('/', [DiscountController::class, 'index']);
        Route::post('/', [DiscountController::class, 'store']);
        Route::get('{id}', [DiscountController::class, 'show'])->where('id', '[0-9]+');
        Route::put('{id}', [DiscountController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('{id}', [DiscountController::class, 'destroy'])->where('id', '[0-9]+');
        Route::get('search', [DiscountController::class, 'search']);
    });
});
