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
        $order = Order::first(); // Получаем первый заказ
        $product = Product::first(); // Получаем первый продукт

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'total_cost' => 900.00, // Стоимость для этой позиции (3 штуки по 300 рублей)
        ]);

        // Можно добавить больше позиций для других заказов
    }
}
