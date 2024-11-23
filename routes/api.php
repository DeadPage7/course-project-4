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

// Получение всех товаров
Route::get('/product', [ProductController::class, 'index']);

// Получение товара по ID
Route::get('/product/{id}', [ProductController::class, 'show']);

// Получение товаров по категории
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category/{id}/products', [CategoryController::class, 'products']);

// Доступ к профилю
Route::middleware('auth:sanctum')->get('profile', [ClientController::class, 'profile']);
Route::middleware('auth:sanctum')->put('profile', [ClientController::class, 'update']);

// Корзина
Route::middleware('auth:sanctum')->group(function () {
    // Получить корзину пользователя
    Route::get('/cart', [CartController::class, 'show']);

    // Добавить товар в корзину
    Route::post('/cart/product/{productId}', [CartController::class, 'addProduct']);

    // Обновить количество товара в корзине
    Route::put('/cart/product/{productId}', [CartController::class, 'updateProduct']);

    // Удалить товар из корзины
    Route::delete('/cart/product/{productId}', [CartController::class, 'removeProduct']);

    // Обновить корзину (например, пересчитать стоимость)
    Route::put('/cart', [CartController::class, 'update']);
});
