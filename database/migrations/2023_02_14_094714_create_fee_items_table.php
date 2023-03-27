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
        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_collection_id')->constrained('fee_collections', 'id');
            $table->foreignId('fee_type_id')->constrained('fee_types', 'id');
            $table->date('period_to')->nullable();
            $table->date('period_from')->nullable();
            $table->string('tenure', 200)->nullable();
            $table->double('amount', 10, 2);
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
        Schema::dropIfExists('fee_items');
    }
};
