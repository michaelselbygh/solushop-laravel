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
            $table->string('id', '15')->primary();
            $table->string('first_name', '70');
            $table->string('last_name', '70');
            $table->string('email', '70')->unique();
            $table->boolean('email_verified')->default('1');
            $table->string('phone', '15')->unique();
            $table->boolean('phone_verified', '15')->default('1');
            $table->string('activation_code', '10');
            $table->string('old_password');
            $table->string('default_address', '5')->default('None');
            $table->string('date_of_birth', '10')->nullable();
            $table->double('milkshake');
            $table->string('icono', '50')->nullable();
            $table->string('sm', '70')->default('SOLUSHOP');
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
        Schema::dropIfExists('customers');
    }
}
