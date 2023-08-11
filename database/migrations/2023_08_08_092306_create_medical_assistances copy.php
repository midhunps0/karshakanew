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
        Schema::create('medical_assistances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members', 'id');
            $table->string('member_name');
            $table->string('member_address');
            $table->string('member_reg_no');
            $table->string('member_reg_date');
            $table->string('member_phone');
            $table->string('member_aadhaar');
            $table->json('member_bank_account');
            $table->date('fee_period_from');
            $table->date('fee_period_to');
            $table->json('medical_bills');
            $table->double('bills_total')->default(0);
            $table->string('hospital_name_address');
            $table->string('patient_mode'); //out/in patient
            $table->date('treatment_period_from');
            $table->date('treatment_period_to');
            $table->float('arrear_months');
            $table->boolean('has_availed')->default(false);
            $table->text('history')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_assistances');
    }
};
