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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type');
            $table->integer('auditable_id');
            $table->string('action')->nullable(); //created,updated,deleted,approved,rejected
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('district_id')->constrained('districts', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
