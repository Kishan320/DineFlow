<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
// Serve Vue SPA for all non-API routes
Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');

    return nl2br(Artisan::output());
});
Route::get('/{any?}', function () {
    return view('welcome');
})->where('any', '.*');
