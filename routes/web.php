<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EtlController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AccessController::class, 'login'])
    ->name('login');

Route::post('/login', [AccessController::class, 'authenticate'])
    ->middleware('throttle:10,1')
    ->name('login.authenticate');

Route::middleware('admin.auth')->group(function () {
    Route::get('/access', [AccessController::class, 'access'])
        ->name('access');

    Route::post('/logout', [AccessController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/drilldown', [DashboardController::class, 'drilldown'])
        ->name('drilldown');

    Route::get('/what-if', [DashboardController::class, 'whatIf'])
        ->name('whatif');

    Route::get('/etl', [EtlController::class, 'index'])
        ->name('etl');

    Route::post('/etl/import', [EtlController::class, 'import'])
        ->name('etl.import');
});
