<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    // Указываем, что эта модель работает с таблицей 'statuses'
    protected $table = 'statuses';

    // Если в таблице есть поля, которые можно массово присваивать
    protected $fillable = ['name'];
}
