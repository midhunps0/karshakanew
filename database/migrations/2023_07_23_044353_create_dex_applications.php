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
        Schema::create('dex_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members', 'id');
            $table->string('member_name');
            $table->date('member_reg_date');
            $table->string('member_address');
            $table->string('applicant_name');
            $table->string('applicant_address');
            $table->string('applicant_phone');
            $table->string('applicant_aadhaar');
            $table->json('applicant_bank_details');
            $table->string('applicant_relation');
            $table->boolean('applicant_is_minor')->default(false);
            $table->date('date_of_death');
            $table->string('marital_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dex_applications');
    }
};
