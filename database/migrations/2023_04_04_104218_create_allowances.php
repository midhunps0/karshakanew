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
        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained(
                'members', 'id'
            );
            $table->string('application_no');
            $table->date('application_date');
            $table->double('applied_amount');
            $table->double('sanctioned_amount');
            $table->date('sanctioned_date');
            $table->date('payment_date');
            $table->foreignId('welfare_scheme_id')->constrained(
                'welfare_schemes', 'id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowances_disbursal');
    }
};
