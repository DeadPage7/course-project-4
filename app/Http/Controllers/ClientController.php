<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ClientController extends Controller
{

    public function profile(Request $request)
    {
        return response()->json($request->user());
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
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:clients,email,' . $request->user()->id,
            'password' => 'nullable|string|min:8|confirmed',
            'login' => 'nullable|string|max:255|unique:clients,login,' . $request->user()->id,
            'birth' => 'nullable|date',
            'telephone' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $client = $request->user();

        // Обновляем только те поля, которые были переданы
        $client->fill($request->only(['full_name', 'email', 'password', 'login', 'birth', 'telephone']));

        if ($request->has('password')) {
            $client->password = Hash::make($request->password);
        }

        $client->save();

        return response()->json($client);
    }



}
