<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

// Регистрация
Route::post('register', [AuthController::class, 'register']);

// Вход
Route::post('login', [AuthController::class, 'login']);

// Выход
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/client', function (Request $request) {
    return response()->json($request->user());
});
