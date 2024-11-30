<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Получение списка всех заказов пользователя
    public function index()
    {
        // Получение текущего авторизованного пользователя
        $user = auth()->user();

        // Получение всех заказов пользователя вместе со статусом и продуктами
        $orders = $user->orders()->with('status', 'items.product')->get();

        // Возврат списка заказов в формате JSON
        return response()->json($orders, 200);
    }


    // Создание нового заказа
    public function store(Request $request)
    {
        // Валидируем адрес
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        // Получаем текущего пользователя и его корзину
        $user = Auth::user();
        $cartProducts = $user->cartProducts;

        // Если корзина пуста, возвращаем ошибку
        if ($cartProducts->isEmpty()) {
            return response()->json(['error' => 'Корзина пуста, невозможно создать заказ'], 400);
        }

        // Вычисляем общую стоимость заказа
        $totalCost = $cartProducts->sum(function ($cartProduct) {
            return $cartProduct->product->price * $cartProduct->quantity;
        });

        // Создаем новый заказ
        $order = new Order();
        $order->client_id = $user->id;
        $order->address_id = $validated['address_id'];
        $order->total_cost = $totalCost;
        $order->status_id = 1; // Например, статус "Принят"
        $order->order_date = now();
        $order->save();

        // Переносим товары из корзины в order_items
        foreach ($cartProducts as $cartProduct) {
            $order->orderItems()->create([
                'product_id' => $cartProduct->product_id,
                'quantity' => $cartProduct->quantity,
                'total_cost' => $cartProduct->product->price * $cartProduct->quantity,
            ]);
        }

        // Очищаем корзину после создания заказа
//        $user->cartProducts()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Заказ успешно создан',
            'order' => $order
        ], 201);
    }

    // Получение данных о заказе
    public function show($id)
    {
        // Находим заказ текущего пользователя
        $order = Auth::user()->orders()->find($id);

        if (!$order) {
            return response()->json(['error' => 'Заказ не найден'], 404);
        }

        return response()->json($order);
    }

    // Обновление заказа (например, обновление адреса или статуса)
    public function update(Request $request, $id)
    {
        // Находим заказ текущего пользователя
        $order = Auth::user()->orders()->find($id);

        if (!$order) {
            return response()->json(['error' => 'Заказ не найден'], 404);
        }

        // Валидируем данные
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'status_id' => 'nullable|integer',
        ]);

        // Обновляем данные заказа
        $order->address_id = $validated['address_id'];
        if (isset($validated['status_id'])) {
            $order->status_id = $validated['status_id'];
        }
        $order->save();

        return response()->json($order);
    }

    // Удаление заказа
    public function destroy($id)
    {
        // Находим заказ текущего пользователя
        $order = Auth::user()->orders()->find($id);

        if (!$order) {
            return response()->json(['error' => 'Заказ не найден'], 404);
        }

        // Удаляем заказ
        $order->delete();

        return response()->json(['message' => 'Заказ удален']);
    }
}
