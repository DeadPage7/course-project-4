<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Создание категорий, если они ещё не существуют
        $category = Category::create([
            'name' => 'Цветущие растения',
        ]);

        // Добавление товара с правильной категорией
        Product::create([
            'name' => 'Роза',
            'price' => 300.00,  // Цена в рублях
            'photo' => 'rose.jpg',
            'description' => 'Красная роза для дома',
            'category_id' => $category->id,  // Получаем id только что созданной категории
        ]);

        // Другие товары
        Product::create([
            'name' => 'Тюльпан',
            'price' => 150.00,
            'photo' => 'tulip.jpg',
            'description' => 'Желтые тюльпаны',
            'category_id' => $category->id,  // Используем id категории
        ]);
    }
}
