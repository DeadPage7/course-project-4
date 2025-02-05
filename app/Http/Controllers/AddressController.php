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
        // Получаем ID текущего аутентифицированного пользователя
        $client_id = auth()->user()->id;

        // Ищем адрес, принадлежащий этому пользователю
        $address = Address::where('id', $id)->where('client_id', $client_id)->first();

        // Если адрес не найден или принадлежит другому пользователю — ошибка 404
        if (!$address) {
            return response()->json(['error' => 'Адрес не найден или у вас нет доступа.'], 404);
        }

        return response()->json($address);
    }



    // Обновление данных адреса
    public function update(Request $request, $id)
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

        // Получаем ID текущего пользователя
        $client_id = auth()->user()->id;

        // Ищем адрес и проверяем, что он принадлежит текущему пользователю
        $address = Address::where('id', $id)->where('client_id', $client_id)->first();

        // Если адрес не найден или принадлежит другому пользователю — возвращаем ошибку
        if (!$address) {
            return response()->json(['error' => 'Адрес не найден или у вас нет прав для его изменения.'], 403);
        }

        // Обновляем данные адреса
        $address->update($request->all());

        // Возвращаем обновленный адрес
        return response()->json($address);
    }


    // Удаление адреса по id
    public function destroy($id)
    {
        // Получаем ID текущего пользователя
        $client_id = auth()->user()->id;

        // Ищем адрес, который принадлежит текущему пользователю
        $address = Address::where('id', $id)->where('client_id', $client_id)->first();

        // Если адрес не найден или принадлежит другому пользователю — возвращаем ошибку
        if (!$address) {
            return response()->json(['error' => 'Адрес не найден или у вас нет прав для его удаления.'], 403);
        }

        // Отвязываем адрес от клиента (ставим client_id = NULL)
        $address->update(['client_id' => null]);

        // Возвращаем успешный ответ
        return response()->json(['message' => 'Адрес успешно отвязан от пользователя.'], 200);
    }


}
