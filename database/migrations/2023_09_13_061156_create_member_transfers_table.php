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
        Schema::create('member_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members', 'id');
            $table->foreignId('from_district_id')->constrained('districts', 'id');
            $table->foreignId('from_taluk_id')->constrained('taluks', 'id');
            $table->foreignId('from_village_id')->constrained('villages', 'id');
            $table->foreignId('district_id')->constrained('districts', 'id');
            $table->foreignId('taluk_id')->constrained('taluks', 'id');
            $table->foreignId('village_id')->constrained('villages', 'id');
            $table->foreignId('requestedby_id')->constrained('users', 'id');
            $table->foreignId('processedby_id')->nullable()->constrained('users', 'id');
            $table->date('request_date');
            $table->date('processed_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_transfers');
    }
};
