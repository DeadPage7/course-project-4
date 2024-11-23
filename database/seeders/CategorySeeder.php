<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Цветущие растения'],
            ['name' => 'Вьющиеся растения'],
            ['name' => 'Суккуленты'],
            ['name' => 'Кактусы'],
            ['name' => 'Декоративные растения'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
