<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); // Добавление client_id как внешнего ключа
            $table->string('city');
            $table->string('street');
            $table->string('house');
            $table->integer('floor')->nullable();
            $table->string('apartment_or_office')->nullable();
            $table->string('entrance')->nullable();
            $table->string('intercom')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
