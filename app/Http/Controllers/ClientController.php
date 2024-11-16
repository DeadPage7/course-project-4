<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    // Получение списка всех клиентов
    public function index()
    {
        return response()->json(Client::all()); // Возвращаем всех клиентов
    }

    // Создание нового клиента
    public function store(Request $request)
    {
        // Валидация данных для создания клиента с кастомными сообщениями
        $request->validate([
            'full_name' => 'required|string|max:255', // Полное имя клиента
            'email' => 'required|email|unique:clients,email|max:255', // Электронная почта, уникальная
            'password' => 'required|string|min:8', // Пароль клиента
            'token' => 'nullable|string|max:255|unique:clients,token', // Токен для клиента
            'login' => 'required|string|max:255|unique:clients,login', // Логин клиента, уникальный
            'birth' => 'required|date', // Дата рождения
            'telephone' => 'nullable|string|max:20', // Телефон (не обязательный)
        ], [
            'full_name.required' => 'Поле "Полное имя" обязательно для заполнения.',
            'email.required' => 'Поле "Электронная почта" обязательно для заполнения.',
            'email.email' => 'Поле "Электронная почта" должно быть валидным адресом электронной почты.',
            'email.unique' => 'Электронная почта уже используется другим клиентом.',
            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.min' => 'Поле "Пароль" должно содержать хотя бы 8 символов.',
            'token.unique' => 'Токен уже используется.',
            'login.required' => 'Поле "Логин" обязательно для заполнения.',
            'login.unique' => 'Логин уже используется другим клиентом.',
            'birth.required' => 'Поле "Дата рождения" обязательно для заполнения.',
            'telephone.max' => 'Поле "Телефон" не может быть длиннее 20 символов.',
        ]);

        // Создание нового клиента в базе данных
        $client = Client::create($request->all());

        // Возвращаем созданного клиента с кодом 201
        return response()->json($client, 201);
    }

    // Получение данных о клиенте по id
    public function show($id)
    {
        // Находим клиента по id, если не найден, выбрасываем ошибку 404
        return response()->json(Client::findOrFail($id));
    }

    // Обновление данных клиента
    public function update(Request $request, $id)
    {
        // Валидация данных для обновления клиента с кастомными сообщениями
        $request->validate([
            'full_name' => 'required|string|max:255', // Полное имя клиента
            'email' => 'required|email|unique:clients,email,' . $id . '|max:255', // Электронная почта
            'password' => 'nullable|string|min:8', // Пароль клиента
            'token' => 'nullable|string|max:255|unique:clients,token,' . $id, // Токен клиента
            'login' => 'required|string|max:255|unique:clients,login,' . $id, // Логин клиента
            'birth' => 'required|date', // Дата рождения
            'telephone' => 'nullable|string|max:20', // Телефон
        ], [
            'full_name.required' => 'Поле "Полное имя" обязательно для обновления.',
            'email.required' => 'Поле "Электронная почта" обязательно для обновления.',
            'email.email' => 'Поле "Электронная почта" должно быть валидным адресом электронной почты.',
            'email.unique' => 'Электронная почта уже используется другим клиентом.',
            'password.min' => 'Поле "Пароль" должно содержать хотя бы 8 символов.',
            'token.unique' => 'Токен уже используется.',
            'login.required' => 'Поле "Логин" обязательно для обновления.',
            'login.unique' => 'Логин уже используется другим клиентом.',
            'birth.required' => 'Поле "Дата рождения" обязательно для обновления.',
            'telephone.max' => 'Поле "Телефон" не может быть длиннее 20 символов.',
        ]);

        // Находим клиента по id
        $client = Client::findOrFail($id);

        // Обновляем данные клиента
        $client->update($request->all());

        // Возвращаем обновленного клиента
        return response()->json($client);
    }

    // Удаление клиента по id
    public function destroy($id)
    {
        // Удаляем клиента по id
        Client::destroy($id);

        // Возвращаем успешный ответ с кодом 204 (без содержимого)
        return response()->json(null, 204);
    }
}
