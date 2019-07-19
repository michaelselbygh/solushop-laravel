<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('order_id')->primary();
            $table->integer('order_type');
            $table->string('order_customer_id');
            $table->string('order_address_id', '10');
            $table->double('order_subtotal');
            $table->double('order_shipping');
            $table->text('order_ad');
            $table->string('order_token');
            $table->string('order_scoupon', '50');
            $table->integer('order_state');
            $table->dateTime('order_date');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
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
