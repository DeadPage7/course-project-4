<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;

// Регистрация
Route::post('register', [AuthController::class, 'register']);

// Вход
Route::post('login', [AuthController::class, 'login']);
// Выход
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::get('client/{id}', [ClientController::class, 'show']);

Route::middleware('auth:sanctum')->get('/client', function (Request $request) {
    return response()->json($request->user());
});
// Получить все товары
Route::get('/product', [ProductController::class, 'index']); // Получить все товары
// Получить товар по ID
Route::get('/product/{id}', [ProductController::class, 'show']); // Получить товар по ID
// Получение товаров по категории
Route::get('/categories', [CategoryController::class, 'index']); // Получить все категории

Route::get('/category/{id}/products', [CategoryController::class, 'products']);

Route::middleware('auth:sanctum')->get('/profile', function (Request $request) {
    return response()->json($request->user());
});
// Получение данных профиля
Route::middleware('auth:sanctum')->get('profile', [ClientController::class, 'profile']);

// Обновление данных профиля
Route::middleware('auth:sanctum')->put('profile', [ClientController::class, 'update']);

Route::middleware('auth:sanctum')->group(function () {
    // Получить корзину клиента
    Route::get('/cart', [CartController::class, 'index']);

    // Создание корзины
    Route::post('/cart', [CartController::class, 'store']);

    // Обновление корзины
    Route::put('/cart/{id}', [CartController::class, 'update']);

    // Добавление товара в корзину
    Route::post('/cart/{cartId}/product', [CartController::class, 'addProduct']);

    // Удаление товара из корзины
    Route::delete('/cart/{cartId}/product/{productId}', [CartController::class, 'removeProduct']);
});
