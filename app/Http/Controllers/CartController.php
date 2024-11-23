<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
    // Получение корзины клиента (единственная корзина)
    // Получение корзины с товарами для пользователя
    public function show(Request $request)
    {
        // Получаем ID клиента из авторизованного пользователя
        $clientId = $request->user()->id;

        // Находим корзину этого клиента
        $cart = Cart::where('client_id', $clientId)->first();

        // Если корзины нет, возвращаем ошибку
        if (!$cart) {
            return response()->json(['message' => 'Корзина не найдена'], 404);
        }

        // Загружаем товары в корзине
        $cart->load('products');

        // Пересчитываем общую стоимость корзины
        $cart->total_cost = $cart->calculateTotalCost();

        // Возвращаем корзину с товарами (без дублирования)
        return response()->json([
            'cart' => [
                'id' => $cart->id,
                'total_cost' => $cart->total_cost,
                'created_at' => $cart->created_at,
                'updated_at' => $cart->updated_at,
                'products' => $cart->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'photo' => $product->photo,
                        'description' => $product->description,
                        'quantity' => $product->pivot->quantity, // Количество товара в корзине
                    ];
                })
            ]
        ]);
    }



    // Обновление корзины (например, обновление общей стоимости)
    public function update(Request $request)
    {
        $cart = $this->getUserCart($request->user()->id);

        $cart->total_cost = $cart->calculateTotalCost();
        $cart->save();

        return response()->json($cart);
    }

    // Добавление товара в корзину
    public function addProduct(Request $request, $productId)
    {
        $cart = $this->getUserCart($request->user()->id);

        // Находим продукт по ID
        $product = Product::findOrFail($productId);

        // Получаем количество из запроса, по умолчанию 1
        $quantity = $request->input('quantity', 1);

        // Проверяем, есть ли уже этот товар в корзине
        $existingProduct = $cart->products()->where('product_id', $productId)->first();

        if ($existingProduct) {
            // Если товар уже есть в корзине, увеличиваем количество
            $newQuantity = $existingProduct->pivot->quantity + $quantity;
            $cart->products()->updateExistingPivot($productId, ['quantity' => $newQuantity]);
        } else {
            // Если товара нет в корзине, добавляем его с количеством
            $cart->products()->attach($productId, ['quantity' => $quantity]);
        }

        // Пересчитываем общую стоимость корзины
        $cart->total_cost = $cart->calculateTotalCost();
        $cart->save();

        return response()->json($cart);
    }

    // Обновление количества товара в корзине
    public function updateProduct(Request $request, $productId)
    {
        $cart = $this->getUserCart($request->user()->id);

        $quantity = $request->input('quantity');
        if ($quantity < 1) {
            return response()->json(['message' => 'Количество товара должно быть больше 0'], 400);
        }

        // Обновляем количество товара в корзине
        $cart->products()->updateExistingPivot($productId, ['quantity' => $quantity]);

        // Обновляем стоимость корзины
        $cart->total_cost = $cart->calculateTotalCost();
        $cart->save();

        return response()->json($cart);
    }

    // Удаление товара из корзины
    public function removeProduct(Request $request, $productId)
    {
        $cart = $this->getUserCart($request->user()->id);

        // Удаляем товар из корзины
        $cart->products()->detach($productId);

        // Обновляем стоимость корзины
        $cart->total_cost = $cart->calculateTotalCost();
        $cart->save();

        return response()->json($cart);
    }

    // Вспомогательный метод для получения корзины пользователя
    private function getUserCart($clientId)
    {
        $cart = Cart::where('client_id', $clientId)->first();
        if (!$cart) {
            throw new ModelNotFoundException('Корзина не найдена');
        }
        return $cart;
    }
}
