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

        // Статус для заказов
        $status = Status::firstOrCreate(
            ['id' => 1],
            ['name' => 'Принят']
        );

        // Получаем первого клиента
        $firstClient = $clients->first();

        // Создаем заказ только для первого клиента
        foreach ($addresses as $address) {
            // Создаем заказ только для первого клиента
            Order::create([
                'client_id' => $firstClient->id,
                'address_id' => $address->id,
                'order_date' => now(),
                'total_cost' => 0, // Стоимость будет вычислена позже
                'status_id' => $status->id,
            ]);
        }

    }
}
