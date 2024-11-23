<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException; // Для обработки исключений

class CategoryController extends Controller
{
    // Получение списка всех категорий
    public function index()
    {
        return response()->json(Category::all()); // Возвращаем все категории
    }
    // Получение товаров по категории
    public function products($id)
    {
        try {
            $category = Category::with('products')->findOrFail($id); // Заранее загружаем товары
            return response()->json($category->products); // Возвращаем только товары
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Категория не найдена.'], 404); // Если категории нет
        }
    }

    // Создание новой категории
    public function store(Request $request)
    {
        // Валидация данных для создания категории с кастомными сообщениями
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name', // Название категории (уникальное)
        ], [
            'name.required' => 'Поле "Название категории" обязательно для заполнения.',
            'name.unique' => 'Категория с таким названием уже существует.',
        ]);

        // Создание новой категории
        $category = Category::create($request->all());

        // Возвращаем информацию о созданной категории с кодом 201
        return response()->json($category, 201);
    }

    // Получение данных о конкретной категории
    public function show($id)
    {
        try {
            // Находим категорию по идентификатору или генерируем исключение, если не найдено
            $category = Category::findOrFail($id);
            return response()->json($category);
        } catch (ModelNotFoundException $e) {
            // Кастомное сообщение об ошибке, если категория не найдена
            return response()->json(['message' => 'Категория с данным идентификатором не найдена.'], 404);
        }
    }

    // Обновление категории
    public function update(Request $request, $id)
    {
        // Валидация данных для обновления категории с кастомными сообщениями
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id, // Название категории (уникальное, кроме текущего)
        ], [
            'name.required' => 'Поле "Название категории" обязательно для обновления.',
            'name.unique' => 'Категория с таким названием уже существует.',
        ]);

        try {
            // Находим категорию по идентификатору
            $category = Category::findOrFail($id);

            // Обновляем категорию с новыми данными
            $category->update($request->all());

            // Возвращаем обновленную категорию
            return response()->json($category);
        } catch (ModelNotFoundException $e) {
            // Кастомное сообщение об ошибке, если категория не найдена
            return response()->json(['message' => 'Категория для обновления не найдена.'], 404);
        }
    }

    // Удаление категории по id
    public function destroy($id)
    {
        try {
            // Находим категорию по идентификатору и удаляем
            $category = Category::findOrFail($id);
            $category->delete();

            // Возвращаем успешный ответ с кодом 204 (нет содержимого)
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            // Кастомное сообщение об ошибке, если категория не найдена
            return response()->json(['message' => 'Категория для удаления не найдена.'], 404);
        }
    }

}
