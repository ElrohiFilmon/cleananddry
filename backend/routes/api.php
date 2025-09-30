<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/check-auth', [AuthController::class, 'checkAuth']);
Route::post('/logout', [AuthController::class, 'logout']);