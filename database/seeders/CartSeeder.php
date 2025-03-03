<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    public function run()
    {
        // Получаем всех клиентов
        $clients = Client::all();
        $products = Product::all(); // Получаем все продукты

        // Добавляем товары в корзину для всех клиентов
        foreach ($clients as $client) {
            $numOfProducts = 3;  // Устанавливаем количество товаров в корзине для каждого клиента

            for ($i = 0; $i < $numOfProducts; $i++) {
                // Выбираем продукт по порядку из списка
                $product = $products[$i % count($products)];  // Циклично выбираем товары
                $quantity = 1;  // Устанавливаем количество товара (1 шт.)

                // Добавляем товар в корзину
                DB::table('carts_products')->insert([
                    'client_id' => $client->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
