<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

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
    });
});

