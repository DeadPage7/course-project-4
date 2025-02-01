<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // Получение списка всех адресов
    public function index()
    {
        // Получаем адреса только для аутентифицированного пользователя
        $user = auth()->user(); // Получаем текущего аутентифицированного пользователя
        return response()->json(Address::where('client_id', $user->id)->get()); // Возвращаем только адреса пользователя
    }

    // Создание нового адреса
    public function store(Request $request)
    {
        // Валидация данных
        $request->validate([
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'house' => 'required|string|max:50',
            'floor' => 'nullable|integer',
            'apartment_or_office' => 'nullable|string|max:50',
            'entrance' => 'nullable|string|max:50',
            'intercom' => 'nullable|string|max:50',
            'comment' => 'nullable|string',
        ]);

        // Получаем ID текущего аутентифицированного пользователя
        $client_id = auth()->user()->id;

        // Создание нового адреса с привязкой к текущему пользователю
        $address = Address::create([
            'city' => $request->city,
            'street' => $request->street,
            'house' => $request->house,
            'floor' => $request->floor,
            'apartment_or_office' => $request->apartment_or_office,
            'entrance' => $request->entrance,
            'intercom' => $request->intercom,
            'comment' => $request->comment,
            'client_id' => $client_id, // Привязываем к текущему пользователю
        ]);

        // Возвращаем ответ с созданным объектом и статусом 201 (создано)
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
