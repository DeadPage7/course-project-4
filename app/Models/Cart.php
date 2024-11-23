<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'total_cost'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_product', 'cart_id', 'product_id')
            ->withPivot('quantity')  // Количество товара
            ->withTimestamps();
    }

    // Вычисление общей стоимости корзины
    public function calculateTotalCost()
    {
        $totalCost = 0;
        foreach ($this->products as $product) {
            $totalCost += $product->price * $product->pivot->quantity; // Цена * Количество
        }
        return $totalCost;
    }
}
