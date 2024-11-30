<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Client;
use App\Models\Address;
use App\Models\Status;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $clients = Client::all();
        $addresses = Address::all();

        $status = Status::firstOrCreate(
            ['id' => 1],
            ['name' => 'Принят']
        );

        foreach ($clients as $client) {
            foreach ($addresses as $address) {
                // Создаем заказ, но оставляем total_cost = 0
                Order::create([
                    'client_id' => $client->id,
                    'address_id' => $address->id,
                    'order_date' => now(),
                    'total_cost' => 0, // Начальная стоимость будет вычислена в контроллере
                    'status_id' => $status->id,
                ]);
            }
        }
    }
}
