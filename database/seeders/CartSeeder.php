<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\Client;

class CartSeeder extends Seeder
{
    public function run()
    {
        $client = Client::first(); // Получаем первого клиента для примера

        Cart::create([
            'client_id' => $client->id,
            'total_cost' => 1500.00, // Цена в рублях
        ]);
    }
}
