<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\Client;
use App\Models\Product;

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
                // Создаем корзину для клиента
                $cart = Cart::create([
                    'client_id' => $client->id,
                    'total_cost' => 0,  // Изначально сумма равна 0
                ]);

                // Добавляем 3 товара в корзину
                $numOfProducts = 3;  // Устанавливаем количество товаров в корзине
                $totalCost = 0;

                for ($i = 0; $i < $numOfProducts; $i++) {
                    // Выбираем продукт по порядку из списка
                    $product = $products[$i % count($products)];  // Циклично выбираем товары
                    $quantity = 1;  // Устанавливаем количество товара (1 шт.)

                    // Добавляем товар в корзину
                    $cart->products()->attach($product->id, ['quantity' => $quantity]);

                    // Обновляем общую стоимость корзины
                    $totalCost += $product->price * $quantity;
                }

                // Обновляем стоимость корзины
                $cart->update(['total_cost' => $totalCost]);
            } else {
                // Для всех остальных клиентов корзина будет пустой
                Cart::create([
                    'client_id' => $client->id,
                    'total_cost' => 0,  // Пустая корзина
                ]);
            }
        }
    }
}
