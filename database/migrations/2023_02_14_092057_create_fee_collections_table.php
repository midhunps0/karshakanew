<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members', 'id');
            $table->foreignId('district_id')->constrained('districts', 'id');
            $table->string('book_number', 100)->default(0);
            $table->string('receipt_number', 100)->default(0);
            $table->double('total_amount', 10, 2)->default(0.00);
            $table->date('receipt_date')->nullable();
            $table->foreignId('payment_mode_id', 300)->nullable()->constrained('payment_modes', 'id');
            // $table->string('collected_by_name', 300)->nullable(); // payment_through
            $table->foreignId('collected_by')->nullable()->constrained('users', 'id');
            // $table->integer('verified_by')->nullable(); // ?? foreignId?
            // $table->integer('status')->default(0);  ???
            $table->text('notes')->nullable(); // ??
            $table->boolean('manual_numbering')->default(false);
            $table->softDeletes();
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
        Schema::dropIfExists('fee_collections');
    }
};
