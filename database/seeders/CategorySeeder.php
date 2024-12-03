<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Модульные растения'],    // 1
            ['name' => 'Инновационные цветы'],   // 2
            ['name' => 'Универсальные культуры'], // 3
            ['name' => 'Минималистичные формы'], // 4
            ['name' => 'Декоративные решения'], // 5
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
