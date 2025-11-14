<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::post('/auth/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/auth/register', [App\Http\Controllers\Api\AuthController::class, 'register']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/auth/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/auth/refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh']);
    
    // Additional API routes can be added here as needed
});