<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWtuPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wtu_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('wtu_payment_customer_id', '25');
            $table->integer('wtu_payment_wtup_id');
            $table->string('wtu_payment_token');
            $table->string('wtu_payment_status', '10');
            $table->string('wtu_tid', '10')->nullable();
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
        Schema::dropIfExists('wtu_payments');
    }
}
