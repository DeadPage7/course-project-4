<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Client;
use App\Models\Address;
use App\Models\Status; // Подключаем модель Status

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Получаем всех клиентов и адреса
        $clients = Client::all();
        $addresses = Address::all();

        // Если в таблице нет статусов, создаем статус с ID 1
        $status = Status::firstOrCreate(
            ['id' => 1],
            ['name' => 'Принят'] // Это можно изменить на нужное значение
        );

        foreach ($clients as $client) {
            foreach ($addresses as $address) {
                Order::create([
                    'client_id' => $client->id,
                    'address_id' => $address->id,
                    'order_date' => now(),
                    'total_cost' => rand(1000, 5000),
                    'status_id' => $status->id, // Указываем status_id
                ]);
            }
        }
    }
}
