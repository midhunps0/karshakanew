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
        Schema::create('marriage_assistance_applications', function (Blueprint $table) {
            $table->foreignId('member_id')->constrained('members', 'id');
            $table->string('member_name');
            $table->id();
            $table->string('member_address');
            $table->string('member_reg_date');
            $table->date('fee_period_from');
            $table->date('fee_period_to');
            $table->float('arrear_months_mrgdt'); //no. of months of arears on marriage date
            $table->string('member_phone');
            $table->string('member_aadhaar');
            $table->json('member_bank_account');
            $table->date('marriage_date');
            $table->string('bride_name');
            $table->string('bride_relation'); //self or daughter
            $table->text('history')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_schemes');
    }
};
