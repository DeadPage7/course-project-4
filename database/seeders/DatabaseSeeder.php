<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Вызов сидеров
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            ClientSeeder::class,
            AddressSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
        ]);
    }
}
