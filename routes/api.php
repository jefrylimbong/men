<?php

use App\Http\Controllers\Api\ActionHistoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\WithdrawalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/customers', [CustomerController::class, 'index']);

    // Rute untuk Update Profil dan Password dari Flutter
    Route::post('/user/update', [AuthController::class, 'updateProfile']);
    Route::post('/user/password', [AuthController::class, 'updatePassword']);

    // Rute untuk Penarikan Kendaraan (Field Worker)
    Route::get('/withdrawals', [WithdrawalController::class, 'index']);
    Route::post('/withdrawals', [WithdrawalController::class, 'store']);
    Route::put('/withdrawals/{id}', [WithdrawalController::class, 'update']);
    Route::get('/withdrawals/masters', [WithdrawalController::class, 'getMasters']);

    // Rute untuk Android Action History
    Route::post('/action-history', [ActionHistoryController::class, 'store']);

    // Rute untuk Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
