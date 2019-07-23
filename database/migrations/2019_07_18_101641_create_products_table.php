<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->integer('product_cid');
            $table->string('product_name');
            $table->string('product_slug')->nullable();
            $table->text('product_description')->nullable();
            $table->text('product_features');
            $table->integer('product_type');
            $table->double('product_settlement_price');
            $table->double('product_selling_price');
            $table->double('product_discount');
            $table->string('product_vid');
            $table->integer('product_views');
            $table->text('product_tags')->nullable();
            $table->integer('product_dd')->default('2');
            $table->double('product_dc');
            $table->integer('product_state');
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
        Schema::dropIfExists('products');
    }
}
