<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('phone', '12');
            $table->string('alt_phone', '12');
            $table->string('email', '70');
            $table->string('address');
            $table->string('passcode');
            $table->string('password')->nullable();
            $table->string('mode_of_payment')->nullable();
            $table->string('payment_details')->nullable();
            $table->double('balance');
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
        Schema::dropIfExists('vendors');
    }
}
