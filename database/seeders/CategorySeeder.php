<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Добавляем категории
        $categories = [
            ['name' => 'Цветущие растения'],
            ['name' => 'Вьющиеся растения'],
        ];

        // Вставляем каждую категорию в базу данных
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
