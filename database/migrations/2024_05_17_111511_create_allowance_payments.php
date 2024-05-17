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
        Schema::create('allowance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allowance_application_id')->constrained('allowances', 'id');
            $table->string('prn_no')->unique();
            $table->date('payment_date');
            $table->float('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowance_payments');
    }
};
