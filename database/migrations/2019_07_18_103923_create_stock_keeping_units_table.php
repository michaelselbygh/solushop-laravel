<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockKeepingUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_keeping_units', function (Blueprint $table) {
            $table->string('id', '10')->primary();
            $table->string('sku_product_id', '20');
            $table->string('sku_variant_description');
            $table->double('sku_settlement_price')->nullable();
            $table->double('sku_selling_price')->nullable();
            $table->double('sku_discount')->nullable();
            $table->integer('sku_stock_left');
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
        Schema::dropIfExists('stock_keeping_units');
    }
}
