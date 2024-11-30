<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Указываем таблицу, если её имя отличается от имени модели в множественном числе
    protected $table = 'order_items';

    // Поля, которые можно массово присваивать
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    // Связь с моделью Order (каждый элемент принадлежит одному заказу)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Связь с моделью Product (каждый элемент связан с одним товаром)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
