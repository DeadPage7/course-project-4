<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
    // Получение корзины с товарами для пользователя
    public function show(Request $request)
    {
        // Получаем ID клиента из авторизованного пользователя
        $clientId = $request->user()->id;

        // Находим корзину этого клиента
        $cartProducts = CartProduct::where('client_id', $clientId)->get();

        // Если корзины нет, возвращаем ошибку
        if ($cartProducts->isEmpty()) {
            return response()->json(['message' => 'Корзина не найдена'], 404);
        }

        // Загружаем товары в корзине
        $products = $cartProducts->map(function ($cartProduct) {
            return [
                'id' => $cartProduct->product->id,
                'name' => $cartProduct->product->name,
                'price' => $cartProduct->product->price,
                'photo' => $cartProduct->product->photo,
                'description' => $cartProduct->product->description,
                'quantity' => $cartProduct->quantity, // Количество товара в корзине
            ];
        });

        // Пересчитываем общую стоимость корзины
        $totalCost = $products->sum(function ($product) {
            return $product['price'] * $product['quantity'];
        });

        // Возвращаем корзину с товарами
        return response()->json([
            'status' => 'success',
            'data' => [
                'cart' => [
                    'total_cost' => $totalCost,
                    'created_at' => $cartProducts->first()->created_at,
                    'updated_at' => $cartProducts->first()->updated_at,
                    'products' => $products,
                ]
            ]
        ]);
    }

    // Обновление корзины ( обновление общей стоимости)
    public function update(Request $request)
    {
        $cartProducts = $this->getUserCartProducts($request->user()->id);

        // Пересчитываем общую стоимость корзины
        $totalCost = $cartProducts->sum(function ($cartProduct) {
            return $cartProduct->product->price * $cartProduct->quantity;
        });

        // Обновляем стоимость корзины
        foreach ($cartProducts as $cartProduct) {
            $cartProduct->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Корзина успешно обновлена',
            'data' => [
                'total_cost' => $totalCost
            ]
        ]);
    }

    // Добавление товара в корзину
    public function addProduct(Request $request, $productId)
    {
        $clientId = $request->user()->id;

        // Находим продукт по ID
        $product = Product::findOrFail($productId);

        // Получаем количество из запроса, по умолчанию 1
        $quantity = $request->input('quantity', 1);

        // Проверяем, есть ли уже этот товар в корзине
        $existingCartProduct = CartProduct::where('client_id', $clientId)
            ->where('product_id', $productId)
            ->first();

        if ($existingCartProduct) {
            // Если товар уже есть в корзине, увеличиваем количество
            $existingCartProduct->quantity += $quantity;
            $existingCartProduct->save();
        } else {
            // Если товара нет в корзине, добавляем его с количеством
            CartProduct::create([
                'client_id' => $clientId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        // Пересчитываем общую стоимость корзины
        $cartProducts = $this->getUserCartProducts($clientId);
        $totalCost = $cartProducts->sum(function ($cartProduct) {
            return $cartProduct->product->price * $cartProduct->quantity;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Товар успешно добавлен в корзину'
        ]);
    }

    // Обновление количества товара в корзине
    public function updateProduct(Request $request, $productId)
    {
        $clientId = $request->user()->id;

        $quantity = $request->input('quantity');
        if ($quantity < 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Количество товара должно быть больше 0'
            ], 400);
        }

        // Обновляем количество товара в корзине
        $cartProduct = CartProduct::where('client_id', $clientId)
            ->where('product_id', $productId)
            ->first();

        if (!$cartProduct) {
            return response()->json([
                'status' => 'error',
                'message' => 'Товар не найден в корзине'
            ], 404);
        }

        $cartProduct->quantity = $quantity;
        $cartProduct->save();

        // Обновляем стоимость корзины
        $cartProducts = $this->getUserCartProducts($clientId);
        $totalCost = $cartProducts->sum(function ($cartProduct) {
            return $cartProduct->product->price * $cartProduct->quantity;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Количество товара в корзине успешно обновлено',
            'data' => [
                'total_cost' => $totalCost
            ]
        ]);
    }

    // Удаление товара из корзины
    public function removeProduct(Request $request, $productId)
    {
        $clientId = $request->user()->id;

        // Удаляем товар из корзины
        CartProduct::where('client_id', $clientId)
            ->where('product_id', $productId)
            ->delete();

        // Обновляем стоимость корзины
        $cartProducts = $this->getUserCartProducts($clientId);
        $totalCost = $cartProducts->sum(function ($cartProduct) {
            return $cartProduct->product->price * $cartProduct->quantity;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Товар успешно удален из корзины'

        ]);
    }

    // Вспомогательный метод для получения корзины пользователя
    private function getUserCartProducts($clientId)
    {
        return CartProduct::where('client_id', $clientId)->get();
    }
}
