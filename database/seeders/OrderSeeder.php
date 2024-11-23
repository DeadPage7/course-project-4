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
        $clients = Client::all();
        $addresses = Address::all();

        foreach ($clients as $client) {
            foreach ($addresses as $address) {
                Order::create([
                    'client_id' => $client->id,
                    'address_id' => $address->id,
                    'order_date' => now(),
                    'total_cost' => rand(1000, 5000),
                ]);
            }
        }
    }
}
