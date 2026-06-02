<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\WaiterController;
use App\Http\Controllers\Api\TaxController;
use App\Http\Controllers\Api\RestaurantSettingsController;

// Guest routes (no auth required)
Route::post('/register',        [AuthController::class, 'register']);
Route::post('/login',           [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',  [AuthController::class, 'resetPassword']);

// Protected routes (require valid Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    Route::apiResource('categories', CategoryController::class)->except(['show']);
    Route::apiResource('items', ItemController::class);
    Route::apiResource('customers', CustomerController::class)->except(['show']);
    Route::apiResource('tables', TableController::class)->except(['show']);
    Route::apiResource('waiters', WaiterController::class)->except(['show']);
    Route::apiResource('taxes', TaxController::class)->except(['show']);

    Route::get('/restaurant-settings',  [RestaurantSettingsController::class, 'index']);
    Route::post('/restaurant-settings', [RestaurantSettingsController::class, 'store']);
    Route::put('/restaurant-settings',  [RestaurantSettingsController::class, 'update']);
});
