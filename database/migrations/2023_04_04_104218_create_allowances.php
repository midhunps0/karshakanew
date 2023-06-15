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
            $table->foreignId('district_id')->constrained(
                'districts', 'id'
            );
            $table->string('allowanceable_type')->nullable();
            $table->integer('allowanceable_id')->nullable();
            $table->string('application_no')->nullable();
            $table->date('application_date');
            $table->double('applied_amount')->nullable();
            $table->double('sanctioned_amount')->nullable();
            $table->date('sanctioned_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->foreignId('welfare_scheme_id')->constrained(
                'welfare_schemes', 'id'
            );
            $table->integer('status')->default(0);
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
