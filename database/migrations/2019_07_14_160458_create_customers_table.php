<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('customer_id', '15')->primary();
            $table->string('first_name', '70');
            $table->string('last_name', '70');
            $table->string('email', '70')->unique();
            $table->boolean('email_verified')->default('1');
            $table->string('phone', '15');
            $table->string('phone_verified', '15')->default('1');
            $table->string('activation_code', '10');
            $table->string('password');
            $table->string('default_address', '5');
            $table->date('date_of_birth')->nullable();
            $table->double('milkshake');
            $table->string('icono', '50')->nullable();
            $table->string('sm', '70')->default('SOLUSHOP');
            $table->rememberToken();
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
        Schema::dropIfExists('customers');
    }
}
