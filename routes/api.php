<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
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

// Товары
Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category/{id}/products', [CategoryController::class, 'products']);

// Профиль
Route::middleware('auth:sanctum')->get('profile', [ClientController::class, 'profile']);
Route::middleware('auth:sanctum')->put('profile', [ClientController::class, 'update']);

// Корзина
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/product/{productId}', [CartController::class, 'addProduct']);
    Route::put('/cart/product/{productId}', [CartController::class, 'updateProduct']);
    Route::delete('/cart/product/{productId}', [CartController::class, 'removeProduct']);
    Route::put('/cart', [CartController::class, 'update']);

    // Заказы
    // Просмотр всех заказов текущего пользователя
    Route::get('/orders', [OrderController::class, 'index'])->middleware('auth');

// Просмотр конкретного заказа текущего пользователя
    Route::get('/orders/{id}', [OrderController::class, 'show'])->middleware('auth');

// Создание нового заказа для текущего пользователя
    Route::post('/orders', [OrderController::class, 'store'])->middleware('auth');

// Удаление заказа текущего пользователя работает походу, но там изменения
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->middleware('auth');

// Просмотр всех позиций заказов (если необходимо, можно сделать доступ только для текущего пользователя)
    Route::get('/order-items', [OrderItemController::class, 'index'])->middleware('auth');

// Создание
    Route::post('/order-items', [OrderItemController::class, 'store'])->middleware('auth');

// Просмотр конкретной позиции заказа
    Route::get('/order-items/{id}', [OrderItemController::class, 'show'])->middleware('auth');

// Удаление
    Route::delete('/order-items/{id}', [OrderItemController::class, 'destroy'])->middleware('auth');
});

