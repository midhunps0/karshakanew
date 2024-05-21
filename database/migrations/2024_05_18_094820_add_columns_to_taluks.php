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
        Schema::table('taluks', function (Blueprint $table) {
            $table->integer('ageover_members')->default(0)->after('enabled');
            $table->integer('inactive_members')->default(0)->after('enabled');
            $table->integer('active_members')->default(0)->after('enabled');
            $table->integer('total_approved_members')->default(0)->after('enabled');
            $table->integer('total_applied_members')->default(0)->after('enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taluks', function (Blueprint $table) {
            $table->dropColumn([
                'ageover_members',
                'inactive_members',
                'active_members',
                'total_approved_members',
                'total_applied_members'
            ]);
        });
    }
};
