<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product_details', function (Blueprint $table) {
            $table->id();
            $table->string("order_transaction_id");
            $table->string("product_id")->nullable();
            $table->string("product_name")->nullable();
            $table->string("color")->nullable();
            $table->string("size")->nullable();
            $table->string("single_price")->nullable();
            $table->string("quantity")->nullable();
            $table->string("total_price")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_product_details');
    }
}
