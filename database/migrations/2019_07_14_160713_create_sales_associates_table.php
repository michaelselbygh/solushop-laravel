<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesAssociatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_associates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name', '70');
            $table->string('last_name', '70');
            $table->string('phone', '12');
            $table->string('email', '70')->unique();
            $table->string('passcode');
            $table->string('password')->nullable();
            $table->string('address');
            $table->integer('badge');
            $table->string('id_type', '100');
            $table->string('id_file');
            $table->string('mode_of_payment');
            $table->string('payment_details');
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
        Schema::dropIfExists('sales_associates');
    }
}
