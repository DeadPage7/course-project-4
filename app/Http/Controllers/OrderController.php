<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Получение списка всех заказов
    public function index()
    {
        return response()->json(Order::all()); // Возвращаем все заказы
    }

    // Создание нового заказа
    public function store(Request $request)
    {
        // Валидация данных для создания заказа с кастомными сообщениями
        $request->validate([
            'client_id' => 'required|exists:clients,id', // Идентификатор клиента
            'address_id' => 'required|exists:addresses,id', // Идентификатор адреса
            'order_date' => 'required|date', // Дата заказа
            'total_cost' => 'required|numeric', // Общая стоимость заказа
        ], [
            'client_id.required' => 'Поле "Идентификатор клиента" обязательно для заполнения.',
            'client_id.exists' => 'Клиент с таким идентификатором не существует.',
            'address_id.required' => 'Поле "Идентификатор адреса" обязательно для заполнения.',
            'address_id.exists' => 'Адрес с таким идентификатором не существует.',
            'order_date.required' => 'Поле "Дата заказа" обязательно для заполнения.',
            'order_date.date' => 'Поле "Дата заказа" должно быть валидной датой.',
            'total_cost.required' => 'Поле "Общая стоимость" обязательно для заполнения.',
            'total_cost.numeric' => 'Поле "Общая стоимость" должно быть числом.',
        ]);

        // Создание нового заказа
        $order = Order::create($request->all());

        // Возвращаем созданный заказ с кодом 201
        return response()->json($order, 201);
    }

    // Получение данных о заказе
    public function show($id)
    {
        // Находим заказ по id или возвращаем ошибку 404
        return response()->json(Order::findOrFail($id));
    }

    // Обновление заказа
    public function update(Request $request, $id)
    {
        // Валидация данных для обновления заказа с кастомными сообщениями
        $request->validate([
            'client_id' => 'required|exists:clients,id', // Идентификатор клиента
            'address_id' => 'required|exists:addresses,id', // Идентификатор адреса
            'order_date' => 'required|date', // Дата заказа
            'total_cost' => 'required|numeric', // Общая стоимость заказа
        ], [
            'client_id.required' => 'Поле "Идентификатор клиента" обязательно для заполнения.',
            'client_id.exists' => 'Клиент с таким идентификатором не существует.',
            'address_id.required' => 'Поле "Идентификатор адреса" обязательно для заполнения.',
            'address_id.exists' => 'Адрес с таким идентификатором не существует.',
            'order_date.required' => 'Поле "Дата заказа" обязательно для заполнения.',
            'order_date.date' => 'Поле "Дата заказа" должно быть валидной датой.',
            'total_cost.required' => 'Поле "Общая стоимость" обязательно для заполнения.',
            'total_cost.numeric' => 'Поле "Общая стоимость" должно быть числом.',
        ]);

        // Находим заказ по id
        $order = Order::findOrFail($id);

        // Обновляем заказ с новыми данными
        $order->update($request->all());

        // Возвращаем обновленный заказ
        return response()->json($order);
    }

    // Удаление заказа
    public function destroy($id)
    {
        // Удаляем заказ по id
        Order::destroy($id);

        // Возвращаем успешный ответ с кодом 204 (без содержимого)
        return response()->json(null, 204);
    }
}
