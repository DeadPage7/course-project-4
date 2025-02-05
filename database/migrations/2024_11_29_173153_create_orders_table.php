<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('address_id')->constrained('addresses');
            $table->date('order_date');
            $table->decimal('total_cost', 10, 2);
            $table->foreignId('status_id')->constrained('statuses')->default(1);
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
