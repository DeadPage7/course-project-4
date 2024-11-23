<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();

        Product::create([
            'name' => 'Роза',
            'price' => 300.00,
            'photo' => 'rose.jpg',
            'description' => 'Красная роза для дома',
            'category_id' => $categories->first()->id,
        ]);

        Product::create([
            'name' => 'Тюльпан',
            'price' => 150.00,
            'photo' => 'tulip.jpg',
            'description' => 'Желтые тюльпаны',
            'category_id' => $categories[0]->id,  // Цветущие растения
        ]);

        Product::create([
            'name' => 'Кактус',
            'price' => 200.00,
            'photo' => 'cactus.jpg',
            'description' => 'Мини кактус для офиса',
            'category_id' => $categories[3]->id,  // Кактусы
        ]);

        Product::create([
            'name' => 'Суккулент',
            'price' => 100.00,
            'photo' => 'succulent.jpg',
            'description' => 'Суккулент для дома',
            'category_id' => $categories[2]->id,  // Суккуленты
        ]);

        Product::create([
            'name' => 'Фикус',
            'price' => 350.00,
            'photo' => 'ficus.jpg',
            'description' => 'Фикус для интерьера',
            'category_id' => $categories[4]->id,  // Декоративные растения
        ]);

        // Добавляем другие продукты
        Product::create([
            'name' => 'Петунья',
            'price' => 120.00,
            'photo' => 'petunia.jpg',
            'description' => 'Цветущая петунья',
            'category_id' => $categories[0]->id,  // Цветущие растения
        ]);

        Product::create([
            'name' => 'Виноградная лоза',
            'price' => 250.00,
            'photo' => 'grapevine.jpg',
            'description' => 'Вьющаяся лоза винограда',
            'category_id' => $categories[1]->id,  // Вьющиеся растения
        ]);

        Product::create([
            'name' => 'Драцена',
            'price' => 400.00,
            'photo' => 'dracaena.jpg',
            'description' => 'Драцена для дома',
            'category_id' => $categories[4]->id,  // Декоративные растения
        ]);

        Product::create([
            'name' => 'Лаванда',
            'price' => 220.00,
            'photo' => 'lavender.jpg',
            'description' => 'Лаванда для сада',
            'category_id' => $categories[0]->id,  // Цветущие растения
        ]);

        Product::create([
            'name' => 'Филодендрон',
            'price' => 180.00,
            'photo' => 'philodendron.jpg',
            'description' => 'Филодендрон для квартиры',
            'category_id' => $categories[4]->id,  // Декоративные растения
        ]);
    }
}
