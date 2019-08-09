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
            $table->string('id')->primary();
            $table->integer('order_type');
            $table->string('order_customer_id');
            $table->string('order_address_id', '10');
            $table->double('order_subtotal');
            $table->double('order_shipping');
            $table->text('order_ad')->nullable();
            $table->string('order_token')->nullable();
            $table->string('order_scoupon', '50')->nullable();
            $table->integer('order_state');
            $table->dateTime('order_date');
            $table->double('dp_shipping')->nullable();
            $table->string('order_tid', '10')->nullable();
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
