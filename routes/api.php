<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
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
// Получить все товары
Route::get('/product', [ProductController::class, 'index']); // Получить все товары
// Получить товар по ID
Route::get('/product/{id}', [ProductController::class, 'show']); // Получить товар по ID
// Получение товаров по категории
Route::get('/category/{id}/products', [CategoryController::class, 'product']);
