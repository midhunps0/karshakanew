<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->integer('display_code')->unique(); // code to map to display ids (old db values) in
            $table->string('name');
            $table->string('short_code');
            $table->boolean('enabled')->default(true);
            $table->string('last_book_no')->default(0);
            $table->integer('last_receipt_no')->default(0);
            $table->integer('last_application_no')->default(0);
            $table->integer('unapproved_members')->nullable();
            $table->integer('pending_applications')->nullable();
            $table->float('this_month_collection')->nullable();
            $table->float('last_week_collection')->nullable();
            $table->float('todays_collection')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('districts');
    }
};
