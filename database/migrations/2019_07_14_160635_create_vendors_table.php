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
            $table->string('vendor_id')->primary();
            $table->string('name');
            $table->string('username');
            $table->string('phone', '12');
            $table->string('alt_phone', '12');
            $table->string('email', '70')->unique();
            $table->string('address');
            $table->string('password');
            $table->double('balance');
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
        Schema::dropIfExists('vendors');
    }
}
