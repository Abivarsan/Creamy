<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Delivergate Ice Cream Shop Management (Creamy)
|--------------------------------------------------------------------------
|
| All routes are prefixed with '/api' automatically by Laravel.
|
*/

// Public Authentication Endpoints
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// Protected Endpoints (Requires Valid Sanctum Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    // Authenticated User / Session Info
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Inventory Items Management (CRUD)
    Route::apiResource('items', ItemController::class);

    // Customer Records Management (CRUD)
    Route::apiResource('customers', CustomerController::class);
});
