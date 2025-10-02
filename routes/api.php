<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Shop\ProductController as ShopProductController;
use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/profile', [AuthController::class, 'profile']);
        });
    });

    Route::prefix('shop')->group(function () {
        Route::apiResource('/products', ShopProductController::class)->only(['index', 'show']);
    });
    
    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
       Route::apiResource('/products', AdminProductController::class)->only(['index', 'show', 'update']);
    });
});