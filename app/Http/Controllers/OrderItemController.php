<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    // Получение списка всех позиций заказа
    public function index()
    {
        return response()->json(OrderItem::all()); // Возвращаем все позиции заказов
    }

    // Создание новой позиции заказа
    public function store(Request $request)
    {
        // Валидация данных для создания позиции заказа с кастомными сообщениями
        $request->validate([
            'order_id' => 'required|exists:orders,id', // Идентификатор заказа
            'product_id' => 'required|exists:products,id', // Идентификатор товара
            'quantity' => 'required|integer', // Количество товара
            'total_cost' => 'required|numeric', // Общая стоимость позиции
        ], [
            'order_id.required' => 'Поле "Идентификатор заказа" обязательно для заполнения.',
            'order_id.exists' => 'Заказ с таким идентификатором не существует.',
            'product_id.required' => 'Поле "Идентификатор товара" обязательно для заполнения.',
            'product_id.exists' => 'Товар с таким идентификатором не существует.',
            'quantity.required' => 'Поле "Количество товара" обязательно для заполнения.',
            'quantity.integer' => 'Поле "Количество товара" должно быть целым числом.',
            'total_cost.required' => 'Поле "Общая стоимость позиции" обязательно для заполнения.',
            'total_cost.numeric' => 'Поле "Общая стоимость позиции" должно быть числом.',
        ]);

        // Создание новой позиции заказа
        $orderItem = OrderItem::create($request->all());

        // Возвращаем созданную позицию с кодом 201
        return response()->json($orderItem, 201);
    }
    
    // Получение данных о позиции заказа
    public function show($id)
    {
        // Находим все позиции для указанного заказа
        $orderItems = OrderItem::where('order_id', $id)->get();

        // Проверяем, если позиции не найдены
        if ($orderItems->isEmpty()) {
            return response()->json(['error' => 'Позиции для данного заказа не найдены'], 404);
        }

        // Возвращаем найденные позиции
        return response()->json($orderItems);
    }


    // Обновление позиции заказа
    public function update(Request $request, $id)
    {
        // Валидация данных для обновления позиции заказа с кастомными сообщениями
        $request->validate([
            'order_id' => 'required|exists:orders,id', // Идентификатор заказа
            'product_id' => 'required|exists:products,id', // Идентификатор товара
            'quantity' => 'required|integer', // Количество товара
            'total_cost' => 'required|numeric', // Общая стоимость позиции
        ], [
            'order_id.required' => 'Поле "Идентификатор заказа" обязательно для заполнения.',
            'order_id.exists' => 'Заказ с таким идентификатором не существует.',
            'product_id.required' => 'Поле "Идентификатор товара" обязательно для заполнения.',
            'product_id.exists' => 'Товар с таким идентификатором не существует.',
            'quantity.required' => 'Поле "Количество товара" обязательно для заполнения.',
            'quantity.integer' => 'Поле "Количество товара" должно быть целым числом.',
            'total_cost.required' => 'Поле "Общая стоимость позиции" обязательно для заполнения.',
            'total_cost.numeric' => 'Поле "Общая стоимость позиции" должно быть числом.',
        ]);

        // Находим позицию заказа по id
        $orderItem = OrderItem::findOrFail($id);

        // Обновляем позицию заказа
        $orderItem->update($request->all());

        // Возвращаем обновленную позицию
        return response()->json($orderItem);
    }

    // Удаление позиции заказа
    public function destroy($id)
    {
        // Удаляем позицию заказа по id
        OrderItem::destroy($id);

        // Возвращаем успешный ответ с кодом 204 (без содержимого)
        return response()->json(null, 204);
    }
}
