<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeletedMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deleted_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('message_sender');
            $table->text('message_content');
            $table->string('message_conversation_id', '10');
            $table->dateTime('message_timestamp');
            $table->string('message_read')->default('Init');
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
        Schema::dropIfExists('deleted_messages');
    }
}
