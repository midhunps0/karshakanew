<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->references('id')->on('districts');
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->references('id')->on('account_groups');
            $table->boolean('is_core_group')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_groups');
    }
}
