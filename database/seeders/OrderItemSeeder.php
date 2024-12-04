<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;

class OrderItemSeeder extends Seeder
{
    public function run()
    {
        // Получаем все заказы для первого клиента
        $firstClientOrder = Order::where('client_id', 1)->first(); // Получаем первый заказ для первого клиента
        $products = Product::all(); // Получаем все товары

        if ($firstClientOrder) {
            $totalCost = 0;  // Общая стоимость товаров в заказе
            $orderItems = []; // Массив для добавления товаров в заказ

            // Выбираем несколько товаров для этого заказа
            $items = $products->random(rand(1, 3)); // Выбираем 1-3 товара для заказа
            foreach ($items as $product) {
                $quantity = rand(1, 5); // Случайное количество товара
                $itemCost = $product->price * $quantity; // Стоимость товара

                // Добавляем товар в массив для сохранения
                $orderItems[] = [
                    'order_id' => $firstClientOrder->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'total_cost' => $itemCost,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Добавляем стоимость этого товара к общей сумме
                $totalCost += $itemCost;
            }

            // Сохраняем все товары в заказ
            OrderItem::insert($orderItems);

            // Обновляем стоимость заказа
            $firstClientOrder->update([
                'total_cost' => $totalCost,
            ]);
        }
    }
}
