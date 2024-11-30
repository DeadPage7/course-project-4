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
        $addresses = Address::all(); // Получаем все адреса

        $status = Status::firstOrCreate(
            ['id' => 1],
            ['name' => 'Принят']
        );

        foreach ($clients as $client) {
            // Выбираем первый адрес из списка адресов клиента
            $address = $addresses->first(); // Выбираем первый адрес

            // Создаём только один заказ для клиента
            Order::create([
                'client_id' => $client->id,
                'address_id' => $address->id, // Используем первый адрес
                'order_date' => now(),
                'total_cost' => 0, // Начальная стоимость будет вычислена в контроллере
                'status_id' => $status->id,
            ]);
        }
    }
}
