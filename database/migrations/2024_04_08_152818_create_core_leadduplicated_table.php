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
        Schema::create('core_leadduplicated', function (Blueprint $table) {
            $table->id();
            $table->string('date', 250);
            $table->string('first_name', 250);
            $table->string('last_name', 250);
            $table->string('country', 250);
            $table->string('date_of_birth', 250);
            $table->string('occupation', 250);
            $table->string('vc', 250);
            $table->string('sdm', 250);
            $table->string('email', 254);
            $table->string('email_alt_1', 254);
            $table->string('email_alt_2', 254);
            $table->string('email_alt_3', 254);
            $table->string('phone_number', 250);
            $table->string('phone_number_alt_1', 250);
            $table->string('phone_number_alt_2', 250);
            $table->string('phone_number_alt_3', 250);
            $table->string('attachment', 250);
            $table->text('private_remark');
            $table->text('remark');
            $table->string('data_source', 250);
            $table->timestampTz('appointment_start_at')->nullable();
            $table->timestampTz('appointment_end_at')->nullable();
            $table->timestampTz('contacted_at')->nullable();
            $table->timestampTz('assignee_read_at')->nullable();
            $table->timestampTz('edited_at');
            $table->timestampTz('created_at');
            $table->bigInteger('appointment_label_id')->nullable();
            $table->bigInteger('assignee_id')->nullable();
            $table->bigInteger('contact_outcome_id')->nullable();
            $table->bigInteger('created_by_id');
            $table->bigInteger('stage_id')->nullable();
            $table->timestampTz('give_up_at')->nullable();
            $table->string('account_manager', 250);
            $table->text('address');
            $table->string('agents_book', 250);
            $table->string('campaign_product', 250);
            $table->string('data_code', 250);
            $table->string('data_type', 250);
            $table->timestampTz('deleted_at')->nullable();
            $table->string('deleted_note', 250);
            $table->uuid('sort_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_leadduplicated');
    }
};
