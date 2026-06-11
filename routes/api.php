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
use App\Http\Controllers\Api\PosSessionController;
use App\Http\Controllers\Api\PosOrderController;
use App\Http\Controllers\Api\PosKotController;
use App\Http\Controllers\Api\PosProductController;
use App\Http\Controllers\Api\PosTableController;
use App\Http\Controllers\Api\PosCustomerController;
use App\Http\Controllers\Api\PosReportController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\StripePaymentController;

// ── Guest routes (no auth required) ──────────────────────────────────────────
// Register is restricted to first-user-only (handled in controller)
Route::post('/register',        [AuthController::class, 'register']);
Route::post('/login',           [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',  [AuthController::class, 'resetPassword']);

// ── Protected routes (require valid Sanctum token) ────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ── Dashboard ────────────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ── Settings — Categories ────────────────────────────────────────────────
    Route::get('categories/list', [CategoryController::class, 'list']);
    Route::apiResource('categories', CategoryController::class)->except(['show']);

    // ── Settings — Items ─────────────────────────────────────────────────────
    Route::apiResource('items', ItemController::class);

    // ── Settings — Customers ─────────────────────────────────────────────────
    Route::apiResource('customers', CustomerController::class)->except(['show']);

    // ── Settings — Tables ────────────────────────────────────────────────────
    Route::apiResource('tables', TableController::class)->except(['show']);

    // ── Settings — Waiters ───────────────────────────────────────────────────
    Route::apiResource('waiters', WaiterController::class)->except(['show']);

    // ── Settings — Taxes ─────────────────────────────────────────────────────
    Route::get('taxes/list', [TaxController::class, 'list']);
    Route::apiResource('taxes', TaxController::class)->except(['show']);

    // ── Restaurant Settings ──────────────────────────────────────────────────
    Route::get('/restaurant-settings',  [RestaurantSettingsController::class, 'index']);
    Route::post('/restaurant-settings', [RestaurantSettingsController::class, 'store']);
    Route::put('/restaurant-settings',  [RestaurantSettingsController::class, 'update']);

    // ── POS Products (for POS screen) ────────────────────────────────────────
    Route::get('pos/products',          [PosProductController::class, 'index']);
    Route::get('pos/categories',        [PosProductController::class, 'categories']);

    // ── POS Tables & Waiters ─────────────────────────────────────────────────
    Route::get('pos/tables',            [PosTableController::class, 'index']);
    Route::get('pos/waiters',           [PosTableController::class, 'waiters']);

    // ── POS Customers ────────────────────────────────────────────────────────
    Route::get('pos/customers/search',  [PosCustomerController::class, 'search']);
    Route::post('pos/customers',        [PosCustomerController::class, 'store']);

    // ── POS Sessions ─────────────────────────────────────────────────────────
    Route::get('pos/session/active',              [PosSessionController::class, 'active']);
    Route::post('pos/session/open',               [PosSessionController::class, 'open']);
    Route::post('pos/session/{session}/close',    [PosSessionController::class, 'close']);
    Route::delete('pos/session/{session}',        [PosSessionController::class, 'destroy']);
    Route::get('pos/session/{session}',           [PosSessionController::class, 'show']);

    // ── POS Orders ───────────────────────────────────────────────────────────
    Route::get('pos/orders/ongoing',              [PosOrderController::class, 'ongoing']);
    Route::get('pos/orders',                      [PosOrderController::class, 'index']);
    Route::post('pos/orders',                     [PosOrderController::class, 'store']);
    Route::get('pos/orders/{posOrder}',           [PosOrderController::class, 'show']);
    Route::patch('pos/orders/{posOrder}/status',  [PosOrderController::class, 'updateStatus']);
    Route::delete('pos/orders/{posOrder}',        [PosOrderController::class, 'destroy']);

    // ── KOT ──────────────────────────────────────────────────────────────────
    Route::get('pos/orders/{posOrder}/kots',      [PosKotController::class, 'index']);
    Route::post('pos/orders/{posOrder}/kots',     [PosKotController::class, 'store']);
    Route::patch('pos/kots/{posKot}/status',      [PosKotController::class, 'updateStatus']);
    Route::get('pos/kots/pending',                [PosKotController::class, 'pending']);

    // ── POS Reports ──────────────────────────────────────────────────────────
    Route::get('pos/reports/daily',               [PosReportController::class, 'dailySales']);
    Route::get('pos/reports/items',               [PosReportController::class, 'itemSales']);
    Route::get('pos/reports/categories',          [PosReportController::class, 'categorySales']);
    Route::get('pos/reports/payments',            [PosReportController::class, 'paymentReport']);
    Route::get('pos/reports/waiters',             [PosReportController::class, 'waiterSales']);
    Route::get('pos/reports/tables',              [PosReportController::class, 'tableSales']);
    Route::get('pos/reports/tax',                 [PosReportController::class, 'taxReport']);
    Route::get('pos/reports/sales',               [PosReportController::class, 'salesList']);
    Route::get('pos/orders/{posOrder}/bill',      [PosReportController::class, 'billData']);

    // ── Report Pages ─────────────────────────────────────────────────────────
    Route::get('reports/cashier',            [ReportController::class, 'cashierReport']);
    Route::get('reports/daily-sales',        [ReportController::class, 'dailySales']);
    Route::get('reports/detailed-sales',     [ReportController::class, 'detailedSales']);
    Route::get('reports/sales-print',        [ReportController::class, 'salesPrint']);
    Route::get('reports/item-wise',          [ReportController::class, 'itemWiseSales']);
    Route::get('reports/consolidated-items', [ReportController::class, 'consolidatedItemWise']);

    // ── Role & Permission Management ─────────────────────────────────────────
    Route::get('permissions',                           [RoleController::class, 'allPermissions']);
    Route::get('roles',                                 [RoleController::class, 'index']);
    Route::post('roles',                                [RoleController::class, 'store']);
    Route::get('roles/{role}',                          [RoleController::class, 'show']);
    Route::put('roles/{role}',                          [RoleController::class, 'update']);
    Route::delete('roles/{role}',                       [RoleController::class, 'destroy']);
    Route::get('roles/{role}/permissions',              [RoleController::class, 'permissions']);
    Route::post('roles/{role}/permissions',             [RoleController::class, 'syncPermissions']);

    // ── User Management (Admin-only, scoped to restaurant) ───────────────────
    Route::get('users',                                 [UserController::class, 'index']);
    Route::post('users',                                [UserController::class, 'store']);
    Route::get('users/{user}',                          [UserController::class, 'show']);
    Route::put('users/{user}',                          [UserController::class, 'update']);
    Route::delete('users/{user}',                       [UserController::class, 'destroy']);
    Route::post('users/{user}/role',                    [UserController::class, 'assignRole']);

    // ── Stripe ───────────────────────────────────────────────────────────────
    Route::post('stripe/create-session',                [StripePaymentController::class, 'createSession']);
    Route::post('stripe/payment-status',                [StripePaymentController::class, 'paymentStatus']);
});
