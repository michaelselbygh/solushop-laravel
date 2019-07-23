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
            $table->bigIncrements('id');
            $table->string('first_name', '70');
            $table->string('last_name', '70');
            $table->string('email', '50')->unique();
            $table->string('phone', '12');
            $table->integer('sms')->default('0');
            $table->integer('access_level')->default('0');
            $table->string('passcode')->default('0');
            $table->string('password')->nullable();
            $table->string('avi')->nullable();
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
        Schema::dropIfExists('managers');
    }
}
