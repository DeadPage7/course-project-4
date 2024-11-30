<?php

namespace App\Http\Controllers;
use App\Models\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Получение списка всех заказов
    public function index()
    {
        // Получаем заказы только для текущего пользователя
        $orders = Auth::user()->orders;
        return response()->json($orders);
    }

    // Создание нового заказа
    public function store(Request $request)
    {
        // Валидируем данные заказа
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'total_cost' => 'required|numeric',
            'products' => 'required|array',  // Валидируем, что 'products' - массив
            'products.*.product_id' => 'required|exists:products,id',  // Валидируем, что каждый товар существует
            'products.*.quantity' => 'required|numeric|min:1',  // Валидируем количество товара
        ]);

        // Создаем заказ для текущего пользователя
        $order = new Order();
        $order->client_id = Auth::id(); // Привязываем заказ к текущему пользователю
        $order->address_id = $validated['address_id'];
        $order->total_cost = $validated['total_cost'];
        $order->status_id = 1; // Например, статус "Принят"
        $order->order_date = now();
        $order->save();

        // Проходим по товарам и добавляем их в таблицу order_items
        foreach ($validated['products'] as $product) {
            // Вычисляем стоимость товара в заказе
            $productModel = Product::find($product['product_id']);
            $totalProductCost = $productModel->price * $product['quantity'];

            // Добавляем товар в order_items
            $order->orderItems()->create([
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'total_cost' => $totalProductCost
            ]);
        }

        return response()->json($order, 201); // Возвращаем созданный заказ
    }


    // Получение данных о заказе
    public function show($id)
    {
        // Находим заказ по идентификатору
        $order = Auth::user()->orders()->find($id);

        if (!$order) {
            return response()->json(['error' => 'Заказ не найден'], 404);
        }

        return response()->json($order);
    }

    // Обновление заказа
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
            'total_cost' => 'required|numeric',
            // Другая валидация
        ]);

        // Обновляем заказ
        $order->address_id = $validated['address_id'];
        $order->total_cost = $validated['total_cost'];
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
