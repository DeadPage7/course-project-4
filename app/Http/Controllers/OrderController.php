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
        // Получаем текущего авторизованного пользователя
        $user = auth()->user();

        // Получаем все заказы пользователя, включая статус и товары
        $orders = $user->orders()->with('status', 'items.product')->get();

        // Пересчитываем общую стоимость для каждого заказа
        $orders = $orders->map(function ($order) {
            // Пересчитываем общую стоимость заказа с учетом всех товаров
            $calculatedTotalCost = $order->items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // Обновляем общую стоимость заказа
            $order->total_cost = $calculatedTotalCost;
            return $order;
        });

        // Возвращаем список заказов с пересчитанными total_cost
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
        $order->total_cost = $totalCost; // Сохраняем общую стоимость
        $order->status_id = 1; // Статус "Принят"
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

        // Очистка корзины после создания заказа
        // $user->cartProducts()->delete(); // Uncomment if needed

        return response()->json([
            'status' => 'success',
            'message' => 'Заказ успешно создан',
            'order' => $order
        ], 201);
    }

    // Получение данных о заказе
    public function show($id)
    {
        // Находим заказ текущего пользователя с его товарами
        $order = Auth::user()->orders()->with('items.product')->find($id);

        if (!$order) {
            return response()->json(['error' => 'Заказ не найден'], 404);
        }

        // Объединяем товары и пересчитываем их параметры
        $groupedItems = $order->items->groupBy('product_id')->map(function ($items) {
            $firstItem = $items->first();

            return [
                'product_id' => $firstItem->product_id,
                'quantity' => $items->sum('quantity'), // Суммируем количество
                'total_cost' => $items->sum(fn($item) => $item->quantity * $item->product->price), // Пересчитываем стоимость
                'product' => $firstItem->product // Информация о продукте
            ];
        })->values();

        // Пересчитываем общую стоимость заказа
        $calculatedTotalCost = $groupedItems->sum('total_cost');

        // Возвращаем заказ с правильной стоимостью и объединенными товарами
        return response()->json([
            'id' => $order->id,
            'client_id' => $order->client_id,
            'address_id' => $order->address_id,
            'order_date' => $order->order_date,
            'total_cost' => $calculatedTotalCost, // Возвращаем пересчитанную стоимость
            'status_id' => $order->status_id,
            'items' => $groupedItems
        ]);
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
