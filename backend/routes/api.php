<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'active'])->group(function () {

    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', fn () => [
            'message' => 'Área do Admin'
        ]);
    });

    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/manager/reports', fn () => [
            'message' => 'Área do Manager'
        ]);

        // User routes
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggle']);

        // Phone routes
        Route::get('/phones', [PhoneController::class, 'index']);
        Route::post('/phones', [PhoneController::class, 'store']);
        Route::get('/phones/{phone}', [PhoneController::class, 'show']);
        Route::put('/phones/{phone}', [PhoneController::class, 'update']);
        Route::delete('/phones/{phone}', [PhoneController::class, 'destroy']);
        Route::get('/users/{user}/phones', [PhoneController::class, 'userPhones']);

        // Address routes
        Route::get('/addresses', [AddressController::class, 'index']);
        Route::post('/addresses', [AddressController::class, 'store']);
        Route::get('/addresses/{address}', [AddressController::class, 'show']);
        Route::put('/addresses/{address}', [AddressController::class, 'update']);
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
        Route::get('/users/{user}/addresses', [AddressController::class, 'userAddresses']);
    });
});

