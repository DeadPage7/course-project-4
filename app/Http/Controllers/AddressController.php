<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    // Получение списка всех адресов
    public function index()
    {
        return response()->json(Address::all()); // Возвращаем все адреса
    }

    // Создание нового адреса
    public function store(Request $request)
    {
        // Валидация данных, переданных в запросе с кастомными сообщениями
        $request->validate([
            'city' => 'required|string|max:255', // Город
            'street' => 'required|string|max:255', // Улица
            'house' => 'required|string|max:50', // Номер дома
            'floor' => 'nullable|integer', // Этаж
            'apartment_or_office' => 'nullable|string|max:50', // Квартира или офис
            'entrance' => 'nullable|string|max:50', // Подъезд
            'intercom' => 'nullable|string|max:50', // Домофон
            'comment' => 'nullable|string', // Комментарий
        ], [
            'city.required' => 'Поле "Город" обязательно для заполнения.',
            'street.required' => 'Поле "Улица" обязательно для заполнения.',
            'house.required' => 'Поле "Номер дома" обязательно для заполнения.',
            'floor.integer' => 'Поле "Этаж" должно быть числом.',
            'apartment_or_office.string' => 'Поле "Квартира или офис" должно быть строкой.',
            'entrance.string' => 'Поле "Подъезд" должно быть строкой.',
            'intercom.string' => 'Поле "Домофон" должно быть строкой.',
            'comment.string' => 'Поле "Комментарий" должно быть строкой.',
        ]);

        // Создание нового адреса в базе данных
        $address = Address::create($request->all());

        // Возвращение ответа с созданным объектом и статусом 201 (создано)
        return response()->json($address, 201);
    }

    // Получение данных о конкретном адресе по его id
    public function show($id)
    {
        try {
            // Ищем адрес по id, если не найден - выбрасываем исключение
            $address = Address::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Если адрес не найден, возвращаем ошибку с кодом 404
            return response()->json(['error' => 'Адрес с таким идентификатором не найден.'], 404);
        }

        // Возвращаем найденный адрес
        return response()->json($address);
    }

    // Обновление данных адреса
    public function update(Request $request, $id)
    {
        // Валидация данных, переданных в запросе с кастомными сообщениями
        $request->validate([
            'city' => 'required|string|max:255', // Город
            'street' => 'required|string|max:255', // Улица
            'house' => 'required|string|max:50', // Номер дома
            'floor' => 'nullable|integer', // Этаж
            'apartment_or_office' => 'nullable|string|max:50', // Квартира или офис
            'entrance' => 'nullable|string|max:50', // Подъезд
            'intercom' => 'nullable|string|max:50', // Домофон
            'comment' => 'nullable|string', // Комментарий
        ], [
            'city.required' => 'Поле "Город" обязательно для обновления.',
            'street.required' => 'Поле "Улица" обязательно для обновления.',
            'house.required' => 'Поле "Номер дома" обязательно для обновления.',
            'floor.integer' => 'Поле "Этаж" должно быть числом.',
            'apartment_or_office.string' => 'Поле "Квартира или офис" должно быть строкой.',
            'entrance.string' => 'Поле "Подъезд" должно быть строкой.',
            'intercom.string' => 'Поле "Домофон" должно быть строкой.',
            'comment.string' => 'Поле "Комментарий" должно быть строкой.',
        ]);

        try {
            // Находим адрес по id, если не найден - выбрасываем исключение
            $address = Address::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Если адрес не найден, возвращаем ошибку с кодом 404
            return response()->json(['error' => 'Адрес с таким идентификатором не найден.'], 404);
        }

        // Обновляем данные адреса
        $address->update($request->all());

        // Возвращаем обновленный адрес
        return response()->json($address);
    }

    // Удаление адреса по id
    public function destroy($id)
    {
        try {
            // Проверка существования адреса перед удалением
            $address = Address::findOrFail($id);
            // Удаление адреса
            $address->delete();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Если адрес не найден, возвращаем ошибку с кодом 404
            return response()->json(['error' => 'Адрес с таким идентификатором не найден.'], 404);
        }

        // Возвращаем успешный ответ с кодом 204 (без содержимого)
        return response()->json(null, 204);
    }
}
