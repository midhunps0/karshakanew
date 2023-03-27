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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //
            $table->string('name_mal')->nullable(); //
            $table->string('membership_no')->nullable();
            $table->foreignId('district_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('district_office_id')->nullable()
                ->constrained('districts', 'id')->onDelete('restrict');
            $table->foreignId('district_residing_id')->nullable()
                ->constrained('districts', 'id')->onDelete('restrict');
            $table->foreignId('taluk_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('village_id')->nullable()->constrained()->onDelete('restrict');
            $table->string('mobile_no')->nullable();
            $table->string('aadhaar_no')->nullable();
            $table->string('election_card_no')->nullable();
            $table->string('eshram_card_no')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable(); //m/f/other
            $table->string('marital_status')->nullable();
            $table->string('parent_guardian')->nullable();
            $table->string('guardian_relationship')->nullable(); //f/m/spouse
            $table->longText('current_address')->nullable();
            $table->longText('current_address_mal')->nullable();
            $table->string('ca_pincode')->nullable();
            $table->longText('permanent_address')->nullable();
            $table->longText('permanent_address_mal')->nullable();
            $table->string('pa_pincode')->nullable();
            $table->string('bank_acc_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_ifsc')->nullable();
            $table->foreignId('trade_union_id')->nullable()->constrained('trade_unions', 'id');
            $table->longText('identification_mark_a')->nullable();
            $table->longText('identification_mark_b')->nullable();
            $table->string('work_locality')->nullable();
            $table->string('local_gov_body_type')->nullable(); //panchayat/municipality/corporation
            $table->date('work_start_date')->nullable();
            $table->foreignId('religion_id')->nullable()->constrained('religions', 'id');
            $table->foreignId('caste_id')->nullable()->constrained('castes', 'id');
            $table->boolean('verified')->default(false);
            $table->boolean('active')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users', 'id');
            $table->timestamp('approved_at')->nullable(); // if null not approved
            $table->foreignId('created_by')->constrained('users', 'id');
            $table->foreignId('last_updated_by')->nullable()->constrained('users', 'id');
            $table->bigInteger('kelt_id')->nullable();
            $table->bigInteger('live_id')->nullable();
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
        Schema::dropIfExists('members');
    }
};
