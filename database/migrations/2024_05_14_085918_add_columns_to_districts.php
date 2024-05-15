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
        Schema::table('districts', function (Blueprint $table) {
            $table->json('last_appl_no_json')->nullable()->after('last_application_no');
            $table->json('snapshot')->nullable()->after('enabled');
            $table->integer('ageover_members')->default(0)->after('enabled');
            $table->integer('inactive_members')->default(0)->after('enabled');
            $table->integer('active_members')->default(0)->after('enabled');
            $table->integer('total_approved_members')->default(0)->after('enabled');
            $table->integer('applied_members')->default(0)->after('enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            $table->dropColumn([
                'last_appl_no_json',
                'snapshot',
                'applied_members',
                'total_approved_members',
                'active_members',
                'inactive_members',
                'ageover_members'
            ]);
        });
    }
};
