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

        // Получаем заказ пользователя, включая статус и товары
        $order = $user->orders()->with('status', 'items.product')->first();

        if (!$order) {
            return response()->json(['error' => 'Заказ не найден'], 404);
        }

        // Пересчитываем общую стоимость для основного заказа
        $calculatedTotalCost = $order->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // Обновляем общую стоимость заказа
        $order->total_cost = $calculatedTotalCost;

        // Возвращаем основной заказ с пересчитанным total_cost
        return response()->json($order, 200);
    }


    // Создание нового заказа
    // Создание нового заказа
    public function store(Request $request)
    {
        // Проверяем, есть ли активный заказ у пользователя
        $user = auth()->user();
        $activeOrder = $user->orders()->where('status_id', '!=', 3) // например, статус 3 - "Завершен"
        ->first();

        if ($activeOrder) {
            return response()->json(['error' => 'У вас уже есть незавершенный заказ'], 400);
        }

        // Валидируем входные данные
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Создаем новый заказ
        $order = $user->orders()->create([
            'address_id' => $validated['address_id'],
            'order_date' => now(),
            'total_cost' => 0, // Стоимость будет пересчитана позже
            'status_id' => 1, // Например, "Принят"
        ]);

        // Добавляем товары в заказ
        $totalCost = 0;
        foreach ($validated['products'] as $productData) {
            $product = Product::find($productData['product_id']);
            $quantity = $productData['quantity'];

            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'total_cost' => $product->price * $quantity,
            ]);

            $totalCost += $product->price * $quantity;
        }

        // Обновляем общую стоимость заказа
        $order->update(['total_cost' => $totalCost]);

        // Возвращаем успешный ответ с данными о заказе
        return response()->json($order, 201);
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
