<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')
                ->constrained('transactions', 'id')
                ->onDelete('cascade');
            $table->foreignId('ledger_account_id')
                ->constrained('ledger_accounts', 'id');
            // $table->foreignId('alias_id')
            //     ->references('id')
            //     ->on('account_aliases')
            //     ->onDelete('restrict');
            $table->bigInteger('client_amount');
            $table->string('action');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_clients');
    }
}
