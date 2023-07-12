<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('district_id')
                ->references('id')
                ->on('districts')
                ->onDelete('restrict');
            $table->string('receipt_voucher_no')->nullable(); //receipt/voucher no.
            $table->date('date');
            $table->bigInteger('amount');
            $table->string('type');
            $table->string('ref_no')->nullable();
            $table->string('instrument_no')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
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
        Schema::dropIfExists('transactions');
    }
}
