<?php

use App\Http\Controllers\Api\V1\AssetController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BalanceController;
use App\Http\Controllers\Api\V1\ConversionFactorController;
use App\Http\Controllers\Api\V1\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::middleware('guest')->prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::apiResource('assets', AssetController::class);
    Route::apiResource('conversion-factors', ConversionFactorController::class);
    Route::apiResource('settings', SettingController::class);

    Route::get('balances', [BalanceController::class, 'index'])->name('balances.index');
    Route::post('balances/convert', [BalanceController::class, 'convert'])->name('balances.convert');
    Route::get('balances/{asset}', [BalanceController::class, 'show'])->name('balances.show');
    Route::patch('balances/{asset}', [BalanceController::class, 'update'])->name('balances.update');
    Route::get('balances/log/{asset}', [BalanceController::class, 'log'])->name('balances.log');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
