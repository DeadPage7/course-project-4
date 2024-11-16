<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Client;
use App\Models\Address;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $client = Client::first(); // Получаем первого клиента
        $address = Address::first(); // Получаем первый адрес

        Order::create([
            'client_id' => $client->id,
            'address_id' => $address->id,
            'order_date' => now(),
            'total_cost' => 1650.00, // Стоимость заказа в рублях
        ]);
    }
}
