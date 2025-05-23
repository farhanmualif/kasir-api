<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\PasswordResetController;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('authenticated', [AuthController::class, 'authenticated']);
// ... existing routes ...
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/verify-token', [PasswordResetController::class, 'verifyToken']); // endpoint baru
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);


Route::middleware(['auth:sanctum'])->group(function () {



    Route::resource('products', ProductController::class);
    Route::resource('barcode', BarcodeController::class);
    Route::resource('user', UserController::class);
    Route::resource('transaction', TransactionController::class);
    Route::resource('category', CategoryController::class);
    Route::apiResource('discounts', DiscountController::class);

    Route::post('products/purchase/existing', [ProductController::class, 'purchaseProductsExist']);
    Route::post('products/upload', [ProductController::class, 'uploadImage']);
    Route::get('products/{barcode}/barcode', [ProductController::class, 'showByBarcode']);
    Route::put('products/{uuid}/image', [ProductController::class, 'updateImage']);
    Route::get('categories/{categoryName}/products', [ProductController::class, 'showByCategory']);
    Route::get('products/images/{uuid}', [ProductController::class, 'showImage']);
    Route::post('categories/{productUuid}/products', [ProductController::class, 'addCategoriesToProduct']);
    Route::put('categories/{uuid}/product', [CategoryController::class, 'updateByProductUuid']);
    Route::delete('categories/{productUuid}/products', [ProductController::class, 'removeCategoriesFromProduct']);

    Route::get('sales/daily/{date}', [RaportController::class, 'getDailySales']);
    Route::get('sales/monthly/{date}', [RaportController::class, 'getMonthlySales']);
    Route::get('sales/yearly/{date}', [RaportController::class, 'getYearlySales']);

    Route::get('purchases/daily/{date}', [RaportController::class, 'getDailyPurchases']);
    Route::get('purchases/monthly/{date}', [RaportController::class, 'getMonthlyPurchases']);
    Route::get('purchases/yearly/{date}', [RaportController::class, 'getYearlyPurchases']);
    Route::get('transaction/{noTransaction}/invoice', [TransactionController::class, 'showInvoice']);
    Route::get('invoices/{noTransaction}', [TransactionController::class, 'showSalesInvoice']);



    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'getUser']);
});
