<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickedUpItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picked_up_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pui_order_item_id', '50');
            $table->string('pui_marked_by_id', '50');
            $table->string('pui_marked_by_description', '100');
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
        Schema::dropIfExists('picked_up_items');
    }
}
