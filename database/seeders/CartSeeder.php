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

        // Получаем все продукты
        $products = Product::all();

        // Для первого и второго клиента добавляем товары в корзину
        foreach ($clients as $index => $client) {
            if ($index == 0 || $index == 1) { // Если это первый или второй клиент
                // Добавляем 3 товара в корзину
                $numOfProducts = 3;  // Устанавливаем количество товаров в корзине

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
}
