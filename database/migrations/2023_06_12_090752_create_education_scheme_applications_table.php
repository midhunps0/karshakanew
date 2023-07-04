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
        Schema::create('education_scheme_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members', 'id');
            $table->string('member_name');
            $table->string('member_address');
            $table->string('student_name');
            $table->json('passed_exam_details');
            $table->float('arrear_months_exdt'); //no. of months of arears on exam date
            $table->boolean('is_sc_st');
            $table->json('marks_scored')->nullable();
            $table->json('total_marks')->nullable();
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
        Schema::dropIfExists('education_schemes');
    }
};
