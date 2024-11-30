<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusesTable extends Migration
{
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id(); // Идентификатор статуса
            $table->string('name'); // Название статуса (например: "Собран", "В пути", "Доставлен")
            $table->timestamps(); // Время создания и обновления
        });
    }

    public function down()
    {
        Schema::dropIfExists('statuses');
    }
}
