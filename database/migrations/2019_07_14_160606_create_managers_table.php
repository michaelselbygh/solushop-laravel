<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('managers', function (Blueprint $table) {
            $table->bigIncrements('manager_id');
            $table->string('first_name', '70');
            $table->string('last_name', '70');
            $table->string('email', '50')->unique();
            $table->string('phone', '12');
            $table->integer('sms')->default('1');
            $table->integer('access_level');
            $table->string('password');
            $table->string('avi');
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
        Schema::dropIfExists('managers');
    }
}
