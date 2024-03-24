<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\User\UserController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
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


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// Route::middleware('auth:sanctum')->resource('users', [UserController::class]);


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     try {
//         return response()->json(User::all());
//     } catch (\Throwable $th) {
//         //throw $th;
//         echo $th;
//     }
// });


Route::middleware(['auth:sanctum', 'require_header'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('transaction', TransactionController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
});
