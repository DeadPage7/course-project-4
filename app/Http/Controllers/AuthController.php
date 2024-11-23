<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Регистрация нового клиента
    public function register(Request $request)
    {
        // Валидация входных данных для регистрации клиента
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255', // Полное имя клиента
            'email' => 'required|string|email|max:255|unique:clients', // Электронная почта (уникальная)
            'password' => 'required|string|min:8|confirmed', // Пароль (минимум 8 символов и подтверждение)
            'login' => 'required|string|max:255|unique:clients', // Логин (уникальный)
            'birth' => 'required|date', // Дата рождения
            'telephone' => 'required|string|max:255', // Телефон
        ]);

        // Если валидация не прошла, возвращаем ошибку с кодом 400
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Создаем нового клиента в базе данных
        $client = Client::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Хешируем пароль
            'login' => $request->login,
            'birth' => $request->birth,
            'telephone' => $request->telephone,
        ]);

        // Генерация токена для нового клиента
        $token = $client->createToken('YourAppName')->plainTextToken;

        // Возвращаем успешное сообщение с данными клиента и токеном
        return response()->json([
            'message' => 'Клиент успешно зарегистрирован!',
            'client' => $client,
            'token' => $token
        ]);
    }

    // Логин (вход) клиента
    public function login(Request $request)
    {
        // Валидация входящих данных для логина
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // Логин
            'password' => 'required|string', // Пароль
        ]);

        // Если валидация не прошла, возвращаем ошибку с кодом 400
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Поиск клиента по логину
        $client = Client::where('login', $request->login)->first();

        // Если клиент не найден или пароль неверный, возвращаем ошибку 401
        if (!$client || !Hash::check($request->password, $client->password)) {
            return response()->json(['message' => 'Неавторизованный доступ'], 401);
        }

        // Генерация нового токена для авторизации с помощью Sanctum
        $token = $client->createToken('YourAppName')->plainTextToken;

        // Возвращаем успешный ответ с данными клиента и новым токеном
        return response()->json([
            'message' => 'Авторизация успешна',
            'client' => $client,
            'token' => $token
        ]);
    }

    // Метод для выхода из системы (удаление всех токенов клиента)
    public function logout(Request $request)
    {
        // Получаем текущего аутентифицированного клиента
        $client = $request->user(); // В случае с Sanctum - это будет клиент, а не пользователь

        // Проверка, что клиент существует (если есть активная сессия)
        if ($client) {
            // Отзываем все токены клиента (выход из системы)
            $client->tokens->each(function ($token) {
                $token->delete(); // Удаляем каждый токен
            });

            // Возвращаем успешный ответ
            return response()->json(['message' => 'Выход из системы успешен']);
        }

        // Если клиент не авторизован, возвращаем ошибку 401
        return response()->json(['message' => 'Неавторизованный доступ'], 401);
    }
}
