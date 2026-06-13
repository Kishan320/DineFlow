<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
// Serve Vue SPA for all non-API routes
Route::get('/storage-link', function () {
    Artisan::call('storage:link');

    return 'Storage linked successfully!';
});
Route::get('/{any?}', function () {
    return view('welcome');
})->where('any', '.*');
