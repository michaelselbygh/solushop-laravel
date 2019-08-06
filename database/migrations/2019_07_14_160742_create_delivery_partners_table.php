<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_partners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('dp_company')->nullable();
            $table->string('first_name', '70');
            $table->string('last_name', '70');
            $table->string('email', '70')->unique();
            $table->string('passcode');
            $table->string('password')->nullable();
            $table->integer('access_level')->default('0');
            $table->string('payment_details')->nullable();
            $table->double('balance')->default('0');
            $table->rememberToken();
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
        Schema::dropIfExists('delivery_partners');
    }
}
