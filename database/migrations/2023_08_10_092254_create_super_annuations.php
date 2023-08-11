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
        Schema::create('super_annuations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members', 'id');
            $table->string('member_name');
            $table->string('member_address');
            $table->string('member_reg_no');
            $table->string('member_reg_date');
            $table->string('member_phone');
            $table->string('member_aadhaar');
            $table->date('member_dob');
            $table->integer('member_age');
            $table->date('fee_period_from');
            $table->date('fee_period_to');
            $table->json('member_bank_account');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_annuations');
    }
};
