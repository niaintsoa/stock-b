<?php

use App\Infrastructure\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes publiques (pas d'authentification requise)
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées (nécessitent un token Bearer)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
