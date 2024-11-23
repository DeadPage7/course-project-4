<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
    // Получение корзины для текущего клиента
    public function index(Request $request)
    {
        $client = $request->user();  // Получаем текущего авторизованного клиента
        $cart = Cart::where('client_id', $client->id)->first();  // Получаем корзину этого клиента

        if (!$cart) {
            return response()->json(['message' => 'Корзина не найдена'], 404);
        }

        return response()->json($cart);
    }

    // Создание корзины для текущего клиента (если корзины нет)
    public function store(Request $request)
    {
        $client = $request->user();  // Получаем текущего авторизованного клиента

        // Проверка, существует ли уже корзина у клиента
        $existingCart = Cart::where('client_id', $client->id)->first();

        if ($existingCart) {
            return response()->json($existingCart);  // Если корзина уже есть, возвращаем её
        }

        // Если корзины нет, создаем новую
        $cart = Cart::create([
            'client_id' => $client->id,
            'total_cost' => 0,  // Изначально пустая корзина, стоимость = 0
        ]);

        return response()->json($cart, 201);
    }

    // Обновление стоимости корзины (когда товар добавлен или удален)
    public function update(Request $request, $id)
    {
        $cart = Cart::findOrFail($id);  // Получаем корзину по id

        // Обновляем общие данные корзины (например, стоимость)
        $cart->update([
            'total_cost' => $request->total_cost,  // Обновляем стоимость
        ]);

        return response()->json($cart);
    }

    // Добавление товара в корзину
    public function addProduct(Request $request, $cartId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::findOrFail($cartId);  // Получаем корзину

        // Проверка, есть ли товар в корзине
        $existingProduct = $cart->products()->where('product_id', $request->product_id)->first();

        if ($existingProduct) {
            // Если товар уже есть в корзине, обновляем количество
            $cart->products()->updateExistingPivot($request->product_id, ['quantity' => $existingProduct->pivot->quantity + $request->quantity]);
        } else {
            // Если товара нет в корзине, добавляем его
            $cart->products()->attach($request->product_id, ['quantity' => $request->quantity]);
        }

        // Пересчитываем общую стоимость корзины
        $cart->total_cost = $cart->calculateTotalCost();
        $cart->save();

        return response()->json($cart);
    }

    // Удаление товара из корзины
    public function removeProduct($cartId, $productId)
    {
        $cart = Cart::findOrFail($cartId);  // Получаем корзину

        // Удаляем товар из корзины
        $cart->products()->detach($productId);

        // Пересчитываем стоимость корзины
        $cart->total_cost = $cart->calculateTotalCost();
        $cart->save();

        return response()->json($cart);
    }
}
