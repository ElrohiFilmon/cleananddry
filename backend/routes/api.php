<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\ReviewController;

// Public routes
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/businesses', [BusinessController::class, 'index']);
Route::get('/businesses/{id}', [BusinessController::class, 'show']);
Route::get('/faqs', [FAQController::class, 'index']);
Route::get('/faqs/{id}', [FAQController::class, 'show']);
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{id}', [ReviewController::class, 'show']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // User location update
    Route::put('/user/location', [AuthController::class, 'updateLocation']);
    Route::get('/check-auth', [AuthController::class, 'checkAuth']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);

    // Business location update
    Route::put('/businesses/{id}/location', [BusinessController::class, 'updateLocation']);

    // Reviews (write/update/delete)
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
});