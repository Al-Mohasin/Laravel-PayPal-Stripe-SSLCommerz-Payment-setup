<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id")->nullable();
            $table->string("name")->nullable();
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->string("country")->nullable();
            $table->string("city")->nullable();
            $table->string("upazila", 50)->nullable();
            $table->string("union_bd", 50)->nullable();
            $table->string("postoffice", 50)->nullable();
            $table->string("address")->nullable();
            $table->string("zip_code")->nullable();
            $table->string("transaction_id")->nullable();
            $table->string("payment_method")->nullable();
            $table->string("currency")->nullable();
            $table->string("amount")->nullable();
            $table->string("coupon", 20)->nullable();
            $table->string("tax")->nullable();
            $table->string("status", 200)->nullable();
            $table->string("delete_status", 50)->default("Active");
            $table->string("day")->nullable();
            $table->string("month")->nullable();
            $table->string("year")->nullable();
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
        Schema::dropIfExists('orders');
    }
}
