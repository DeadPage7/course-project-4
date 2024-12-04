<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\Client;

class AddressSeeder extends Seeder
{
    public function run()
    {
        $client = Client::first(); // Получаем первого клиента для примера

        // Проверяем, если у клиента уже есть адрес, не создаем новый
        if (!$client->addresses()->exists()) {
            Address::create([
                'client_id' => $client->id,
                'city' => 'Москва',
                'street' => 'Тверская улица',
                'house' => '12',
                'floor' => 3,
                'apartment_or_office' => 'Квартира 101',
                'entrance' => 'Главный вход',
                'intercom' => '101',
                'comment' => 'Оставить посылку с охранником',
            ]);
        }
    }
}
