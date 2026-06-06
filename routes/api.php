<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\TransactionsController;
use App\Http\Controllers\Api\TransfersController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', MeController::class);
    Route::get('/dashboard', DashboardController::class);
    Route::get('/transactions', TransactionsController::class);
    Route::post('/transfers', [TransfersController::class, 'store']);
});
