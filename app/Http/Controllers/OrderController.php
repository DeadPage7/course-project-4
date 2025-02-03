<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\CartProduct;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Получение списка всех заказов пользователя
    public function index()
    {
        // Получаем текущего авторизованного пользователя
        $user = auth()->user();

        // Получаем все заказы пользователя, включая статус и товары
        $orders = $user->orders()->with('status','items.product')->get();

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
        $clientId = $request->user()->id;

        // Получаем address_id из запроса
        $addressId = $request->input('address_id');

        // Получаем адрес по переданному address_id и проверяем, что он принадлежит текущему пользователю
        $address = Address::where('client_id', $clientId)->where('id', $addressId)->first();

        // Если адрес не найден или не принадлежит текущему пользователю
        if (!$address) {
            return response()->json([
                'status' => 'error',
                'message' => 'Не удалось найти указанный адрес. Пожалуйста, выберите адрес, который вам принадлежит.',
            ], 400); // Возвращаем статус 400 при ошибке
        }

        // Получаем товары из корзины
        $cartProducts = CartProduct::where('client_id', $clientId)->with('product')->get();

        // Если корзина пуста
        if ($cartProducts->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Корзина пуста. Добавьте товары для оформления заказа.',
            ], 400);
        }

        // Рассчитываем общую стоимость
        $totalCost = $cartProducts->sum(fn($cartProduct) => $cartProduct->product->price * $cartProduct->quantity);

        try {
            DB::transaction(function () use ($clientId, $address, $cartProducts, $totalCost) {
                // Создаём заказ
                $order = Order::create([
                    'client_id' => $clientId,
                    'address_id' => $address->id,
                    'order_date' => now(),
                    'total_cost' => $totalCost,
                    'status_id' => 1, // Установите подходящий статус
                ]);

                // Добавляем товары в заказ
                $orderItems = $cartProducts->map(function ($cartProduct) use ($order) {
                    return [
                        'order_id' => $order->id,
                        'product_id' => $cartProduct->product_id,
                        'quantity' => $cartProduct->quantity,
                        'total_cost' => $cartProduct->product->price * $cartProduct->quantity,
                    ];
                });

                // Массовая вставка в order_items
                OrderItem::insert($orderItems->toArray());

                // Очищаем корзину
                CartProduct::where('client_id', $clientId)->delete();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Заказ успешно оформлен.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка при создании заказа: ' . $e->getMessage(),
            ], 500);
        }
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
                'quantity' => $items->sum('quantity'), // Суммируем количество
                'total_cost' => (string) number_format($items->sum(fn($item) => $item->quantity * $item->product->price), 2, '.', ''), // Преобразуем в строку с двумя знаками после запятой
                'product' => $firstItem->product // Информация о продукте
            ];
        })->values();

        // Пересчитываем общую стоимость заказа (оставляем числом)
        $calculatedTotalCost = $groupedItems->sum(fn($item) => (float) $item['total_cost']); // Переводим обратно в число для корректного подсчета

        // Возвращаем заказ с правильной стоимостью и объединенными товарами
        return response()->json([
            'id' => $order->id,
            'client_id' => $order->client_id,
            'address_id' => $order->address_id,
            'order_date' => $order->order_date,
            'total_cost' => $calculatedTotalCost, // Оставляем числом
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
