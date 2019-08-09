<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVsPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vs_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vs_payment_vendor_id', '20');
            $table->integer('vs_payment_vsp_id');
            $table->integer('vs_payment_vspq');
            $table->double('vs_payment_amount');
            $table->string('vs_payment_token');
            $table->string('vs_payment_type', '20');
            $table->string('vs_payment_state', '10');
            $table->string('vs_payment_tid', '10')->nullable();
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
        Schema::dropIfExists('vs_payments');
    }
}
