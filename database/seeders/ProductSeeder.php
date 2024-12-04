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
            'name' => 'Альтерия',
            'price' => 300.00,
            'photo' => 'products/image1.png',
            'description' => 'Растение, которое станет идеальным дополнением любого пространства.',
            'category_id' => $categories[0]->id, // Модульные растения
        ]);

        Product::create([
            'name' => 'Версия',
            'price' => 150.00,
            'photo' => 'products/image2.png',
            'description' => 'Подходит для тех, кто ценит минимализм и простоту.',
            'category_id' => $categories[3]->id, // Минималистичные формы
        ]);

        Product::create([
            'name' => 'Синтексия',
            'price' => 200.00,
            'photo' => 'products/image3.png',
            'description' => 'Идеальный выбор для создания уютной атмосферы.',
            'category_id' => $categories[4]->id, // Декоративные решения
        ]);

        Product::create([
            'name' => 'Эвокс',
            'price' => 100.00,
            'photo' => 'products/image4.png',
            'description' => 'Растение, которое радует своей неприхотливостью.',
            'category_id' => $categories[2]->id, // Универсальные культуры
        ]);

        Product::create([
            'name' => 'Десфера',
            'price' => 350.00,
            'photo' => 'products/image5.png',
            'description' => 'Прекрасно подойдет для декорирования офиса или дома.',
            'category_id' => $categories[4]->id, // Декоративные решения
        ]);

        Product::create([
            'name' => 'Флурия',
            'price' => 120.00,
            'photo' => 'products/image6.png',
            'description' => 'Легко выращивается и добавляет живости в интерьер.',
            'category_id' => $categories[0]->id, // Модульные растения
        ]);

        Product::create([
            'name' => 'Гирофлора',
            'price' => 250.00,
            'photo' => 'products/image7.png',
            'description' => 'Универсальное растение для оформления различных пространств.',
            'category_id' => $categories[1]->id, // Инновационные цветы
        ]);

        Product::create([
            'name' => 'Калидора',
            'price' => 400.00,
            'photo' => 'products/image8.png',
            'description' => 'Отличный выбор для тех, кто любит экспериментировать с растениями.',
            'category_id' => $categories[4]->id, // Декоративные решения
        ]);

        Product::create([
            'name' => 'Элатия',
            'price' => 220.00,
            'photo' => 'products/image9.png',
            'description' => 'Универсальное растение с богатой историей.',
            'category_id' => $categories[0]->id, // Модульные растения
        ]);

        Product::create([
            'name' => 'Норестия',
            'price' => 180.00,
            'photo' => 'products/image10.png',
            'description' => 'Хорошо вписывается в любую обстановку, добавляя уюта.',
            'category_id' => $categories[4]->id, // Декоративные решения
        ]);
    }
}
