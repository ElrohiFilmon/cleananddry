<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/businesses', [BusinessController::class, 'index']);
Route::get('/businesses/{id}', [BusinessController::class, 'show']);

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!', 'timestamp' => now()]);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/check-auth', [AuthController::class, 'checkAuth']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Business management
    Route::put('/businesses/{id}', [BusinessController::class, 'update']);
});
Route::get('/check-session', function (Request $request) {
    return response()->json([
        'authenticated' => Auth::check(),
        'user' => Auth::check() ? Auth::user() : null,
        'session_id' => session()->getId()
    ]);
});