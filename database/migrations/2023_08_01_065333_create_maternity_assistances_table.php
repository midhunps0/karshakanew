<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maternity_assistances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members', 'id');
            $table->string('member_name');
            $table->string('member_address');
            $table->string('member_reg_no');
            $table->string('member_reg_date');
            $table->date('fee_period_from');
            $table->date('fee_period_to');
            $table->date('delivery_date');
            $table->float('arrear_months_dlrydt'); //no. of months of arears on marriage date
            $table->integer('previous_availed_counts')->default(0);
            $table->string('member_phone');
            $table->string('member_aadhaar');
            $table->json('member_bank_account');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maternity_assistances');
    }
};
