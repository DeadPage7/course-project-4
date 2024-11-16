<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException; // Для обработки исключений

class CartController extends Controller
{
    // Получение списка всех корзин
    public function index()
    {
        return response()->json(Cart::all()); // Возвращаем все корзины
    }

    // Создание новой корзины
    public function store(Request $request)
    {
        // Валидация данных для создания корзины с кастомными сообщениями
        $request->validate([
            'client_id' => 'required|exists:clients,id', // Идентификатор клиента (должен существовать)
            'total_cost' => 'required|numeric', // Общая стоимость товаров в корзине
        ], [
            'client_id.required' => 'Поле "Идентификатор клиента" обязательно для заполнения.',
            'client_id.exists' => 'Клиент с таким идентификатором не существует.',
            'total_cost.required' => 'Поле "Общая стоимость" обязательно для заполнения.',
            'total_cost.numeric' => 'Поле "Общая стоимость" должно быть числом.',
        ]);

        // Создание новой корзины
        $cart = Cart::create($request->all());

        // Возвращаем информацию о созданной корзине с кодом 201
        return response()->json($cart, 201);
    }

    // Получение данных о конкретной корзине
    public function show($id)
    {
        try {
            // Находим корзину по идентификатору или генерируем исключение, если не найдено
            $cart = Cart::findOrFail($id);
            return response()->json($cart);
        } catch (ModelNotFoundException $e) {
            // Кастомное сообщение об ошибке, если корзина не найдена
            return response()->json(['message' => 'Корзина с данным идентификатором не найдена.'], 404);
        }
    }

    // Обновление корзины
    public function update(Request $request, $id)
    {
        // Валидация данных для обновления корзины с кастомными сообщениями
        $request->validate([
            'client_id' => 'required|exists:clients,id', // Идентификатор клиента (должен существовать)
            'total_cost' => 'required|numeric', // Общая стоимость товаров
        ], [
            'client_id.required' => 'Поле "Идентификатор клиента" обязательно для обновления.',
            'client_id.exists' => 'Клиент с таким идентификатором не существует.',
            'total_cost.required' => 'Поле "Общая стоимость" обязательно для обновления.',
            'total_cost.numeric' => 'Поле "Общая стоимость" должно быть числом.',
        ]);

        try {
            // Находим корзину по идентификатору
            $cart = Cart::findOrFail($id);

            // Обновляем корзину с новыми данными
            $cart->update($request->all());

            // Возвращаем обновленную корзину
            return response()->json($cart);
        } catch (ModelNotFoundException $e) {
            // Кастомное сообщение об ошибке, если корзина не найдена
            return response()->json(['message' => 'Корзина для обновления не найдена.'], 404);
        }
    }

    // Удаление корзины по id
    public function destroy($id)
    {
        try {
            // Находим корзину по идентификатору и удаляем
            $cart = Cart::findOrFail($id);
            $cart->delete();

            // Возвращаем успешный ответ с кодом 204 (нет содержимого)
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            // Кастомное сообщение об ошибке, если корзина не найдена
            return response()->json(['message' => 'Корзина для удаления не найдена.'], 404);
        }
    }
}
