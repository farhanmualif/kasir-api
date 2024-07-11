<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CategoryController;
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


Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('product/upload', [ProductController::class, 'uploadImage']);
    Route::resource('products', ProductController::class);
    Route::put('products/{uuid}/image', [ProductController::class, 'updateImage']);
    Route::resource('transaction', TransactionController::class);
    Route::resource('category', CategoryController::class);
    Route::put('category-product/{uuid}', [CategoryController::class, 'updateByProductUuid']);
    Route::resource('barcode', BarcodeController::class);
    Route::resource('user', UserController::class);

    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('sales/daily/{date}', [RaportController::class, 'getDailySales']);
    Route::get('sales/monthly/{date}', [RaportController::class, 'getMonthlySales']);
    Route::get('sales/yearly/{date}', [RaportController::class, 'getYearlySales']);

    Route::get('purchases/daily/{date}', [RaportController::class, 'getDailyPurchases']);
    Route::get('purchases/monthly/{date}', [RaportController::class, 'getMonthlyPurchases']);
    Route::get('purchases/yearly/{date}', [RaportController::class, 'getYearlyPurchases']);
});
