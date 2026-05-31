<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\WaiterController;
use App\Http\Controllers\Api\TaxController;

Route::apiResource('categories', CategoryController::class)->except(['show']);
Route::apiResource('items', ItemController::class);
Route::apiResource('customers', CustomerController::class)->except(['show']);
Route::apiResource('tables', TableController::class)->except(['show']);
Route::apiResource('waiters', WaiterController::class)->except(['show']);
Route::apiResource('taxes', TaxController::class)->except(['show']);
