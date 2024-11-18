<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Получение списка всех товаров
    public function index()
    {
        return response()->json(Product::all()); // Возвращаем все товары
    }

    // Создание нового товара
    public function store(Request $request)
    {
        // Валидация данных для создания товара с кастомными сообщениями
        $request->validate([
            'name' => 'required|string|max:255', // Название товара
            'price' => 'required|numeric', // Цена товара
            'photo' => 'required|string', // Фото товара
            'description' => 'required|string', // Описание товара
            'category_id' => 'required|exists:categories,id', // Идентификатор категории
        ], [
            'name.required' => 'Поле "Название товара" обязательно для заполнения.',
            'name.string' => 'Поле "Название товара" должно быть строкой.',
            'name.max' => 'Поле "Название товара" не должно превышать 255 символов.',
            'price.required' => 'Поле "Цена товара" обязательно для заполнения.',
            'price.numeric' => 'Поле "Цена товара" должно быть числом.',
            'photo.required' => 'Поле "Фото товара" обязательно для заполнения.',
            'photo.string' => 'Поле "Фото товара" должно быть строкой.',
            'description.required' => 'Поле "Описание товара" обязательно для заполнения.',
            'description.string' => 'Поле "Описание товара" должно быть строкой.',
            'category_id.required' => 'Поле "Идентификатор категории" обязательно для заполнения.',
            'category_id.exists' => 'Категория с таким идентификатором не существует.',
        ]);

        // Создание нового товара
        $product = Product::create($request->all());

        // Возвращаем созданный товар с кодом 201
        return response()->json($product, 201);
    }

    // Получение данных о товаре
    public function show($id)
    {
        // Находим товар по id или возвращаем ошибку 404
        return response()->json(Product::findOrFail($id));
    }

    // Обновление данных товара
    public function update(Request $request, $id)
    {
        // Валидация данных для обновления товара с кастомными сообщениями
        $request->validate([
            'name' => 'required|string|max:255', // Название товара
            'price' => 'required|numeric', // Цена товара
            'photo' => 'required|string', // Фото товара
            'description' => 'required|string', // Описание товара
            'category_id' => 'required|exists:categories,id', // Идентификатор категории
        ], [
            'name.required' => 'Поле "Название товара" обязательно для заполнения.',
            'name.string' => 'Поле "Название товара" должно быть строкой.',
            'name.max' => 'Поле "Название товара" не должно превышать 255 символов.',
            'price.required' => 'Поле "Цена товара" обязательно для заполнения.',
            'price.numeric' => 'Поле "Цена товара" должно быть числом.',
            'photo.required' => 'Поле "Фото товара" обязательно для заполнения.',
            'photo.string' => 'Поле "Фото товара" должно быть строкой.',
            'description.required' => 'Поле "Описание товара" обязательно для заполнения.',
            'description.string' => 'Поле "Описание товара" должно быть строкой.',
            'category_id.required' => 'Поле "Идентификатор категории" обязательно для заполнения.',
            'category_id.exists' => 'Категория с таким идентификатором не существует.',
        ]);

        // Находим товар по id
        $product = Product::findOrFail($id);

        // Обновляем товар
        $product->update($request->all());

        // Возвращаем обновленный товар
        return response()->json($product);
    }

    // Удаление товара
    public function destroy($id)
    {
        // Удаляем товар по id
        Product::destroy($id);

        // Возвращаем успешный ответ с кодом 204 (без содержимого)
        return response()->json(null, 204);
    }
}
