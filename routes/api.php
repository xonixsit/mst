<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::post('/auth/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/auth/register', [App\Http\Controllers\Api\AuthController::class, 'register']);

// Public lead submission (no auth required)
Route::post('/leads', [App\Http\Controllers\LeadController::class, 'store']);

// Test route
Route::post('/test-leads', function(Request $request) {
    return response()->json(['success' => true, 'data' => $request->all()]);
});

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/auth/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/auth/refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh']);
    
    // Additional API routes can be added here as needed
});