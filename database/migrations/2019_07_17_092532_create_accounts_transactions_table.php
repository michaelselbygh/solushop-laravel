<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('trans_type');
            $table->double('trans_amount');
            $table->integer('trans_credit_account_type')->nullable();
            $table->string('trans_credit_account', '70')->nullable();
            $table->integer('trans_debit_account_type')->nullable();
            $table->string('trans_debit_account', '70')->nullable();
            $table->string('trans_description');
            $table->dateTime('trans_date');
            $table->string('trans_recorder', '70')->default('System');
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
        Schema::dropIfExists('accounts_transactions');
    }
}
