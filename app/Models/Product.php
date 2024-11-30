<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category; // Добавьте эту строку

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price', 'photo', 'description', 'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class); // Убедитесь, что Category подключена
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
