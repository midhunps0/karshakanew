<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLedgerAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ledger_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')
                ->references('id')
                ->on('districts')
                ->onDelete('restrict');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('group_id')->references('id')->on('account_groups');
            $table->bigInteger('opening_balance')->nullable();
            $table->string('opening_bal_type')->nullable(); //debit or credit
            $table->boolean('cashorbank')->default(false);
            // $table->string('type');
            $table->timestamps();
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
        Schema::dropIfExists('ledger_accounts');
    }
}
