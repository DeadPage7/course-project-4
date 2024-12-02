<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

// Авторизация
Route::post('register', [AuthController::class, 'register']); // Регистрация
Route::post('login', [AuthController::class, 'login']);       // Вход
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']); // Выход

// Товары и категории
Route::get('/product', [ProductController::class, 'index']);      // Все товары
Route::post('/product', [ProductController::class, 'store']);      // Все товары
Route::get('/product/{id}', [ProductController::class, 'show']);  // Конкретный товар
Route::get('/categories', [CategoryController::class, 'index']); // Все категории
Route::get('/category/{id}/products', [CategoryController::class, 'products']); // Товары категории

// Защищенные маршруты
Route::middleware('auth:sanctum')->group(function () {
    // Профиль
    Route::get('profile', [ClientController::class, 'profile']); // Просмотр профиля
    Route::put('profile', [ClientController::class, 'update']);  // Обновление профиля

    // Корзина
    Route::get('/cart', [CartController::class, 'show']);                          // Просмотр корзины
    Route::post('/cart/product/{productId}', [CartController::class, 'addProduct']); // Добавление товара в корзину
    Route::put('/cart/product/{productId}', [CartController::class, 'updateProduct']); // Обновление товара в корзине
    Route::delete('/cart/product/{productId}', [CartController::class, 'removeProduct']); // Удаление товара из корзины
    Route::put('/cart', [CartController::class, 'update']);                          // Обновление корзины

    // Заказы
    Route::get('/orders', [OrderController::class, 'index']);           // Все заказы текущего пользователя
    Route::get('/orders/{id}', [OrderController::class, 'show']);       // Просмотр конкретного заказа
    Route::post('/orders', [OrderController::class, 'store']);          // Создание нового заказа
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']); // Удаление заказа

    // Позиции заказов (если нужно)
    Route::get('/order-items', [OrderItemController::class, 'index']);     // Все позиции заказов
    Route::get('/order-items/{id}', [OrderItemController::class, 'show']); // Конкретная позиция заказа
});
