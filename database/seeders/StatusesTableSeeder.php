<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusesTableSeeder extends Seeder
{
    public function run()
    {
        // Создаем статусы
        Status::create(['id' => 1], ['name' => 'Принят']);
        Status::create(['id' => 2], ['name' => 'В пути']);
        Status::create(['id' => 3], ['name' => 'Доставлен']);
    }
}

