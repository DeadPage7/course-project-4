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
        $orders = Order::all(); // Получаем все заказы
        $products = Product::all(); // Получаем все товары

        foreach ($orders as $order) {
            // Выбираем несколько товаров для этого заказа
            $items = $products->random(rand(1, 3)); // Выбираем 1-3 товара для каждого заказа
            foreach ($items as $product) {
                $quantity = rand(1, 5); // Случайное количество товара
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'total_cost' => $product->price * $quantity, // Стоимость товара
                ]);
            }
        }
    }
}
