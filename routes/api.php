<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::middleware(['log.api.requests'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-sms', [AuthController::class, 'verifySms']);
    Route::post('/resend-sms', [AuthController::class, 'resendSms']);
    
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'log.api.requests'])->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/change-phone', [UserController::class, 'changePhone']);
    Route::post('/update-avatar', [UserController::class, 'updateAvatar']);
    Route::post('/logout', [UserController::class, 'logout']);
    

    Route::get('/my-orders', [OrderController::class, 'myOrders']);
    Route::post('/orders', [OrderController::class, 'store']);
    
    Route::middleware(['role:admin'])->group(function () {

        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::put('/orders/{id}', [OrderController::class, 'update']);
        Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
    });
});

Route::post('/payme', [PaymeController::class, 'payme']);