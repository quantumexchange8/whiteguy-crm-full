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
        Schema::create('duplicated_leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('country')->nullable();
            $table->text('address')->nullable();
            $table->dateTime('date_oppd_in')->nullable();
            $table->string('campaign_product')->nullable();
            $table->string('sdm')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('occupation')->nullable();
            $table->string('agents_book')->nullable();
            $table->string('account_manager')->nullable();
            $table->string('vc');
            $table->string('data_type')->nullable();
            $table->string('data_source')->nullable();
            $table->string('data_code')->nullable();
            $table->string('email');
            $table->string('email_alt_1')->nullable();
            $table->string('email_alt_2')->nullable();
            $table->string('email_alt_3')->nullable();
            $table->integer('phone_number');
            $table->integer('phone_number_alt_1')->nullable();
            $table->integer('phone_number_alt_2')->nullable();
            $table->integer('phone_number_alt_3')->nullable();
            $table->text('private_remark')->nullable();
            $table->text('remark')->nullable();
            $table->dateTime('appointment_start_at')->nullable();
            $table->dateTime('appointment_end_at')->nullable();
            $table->dateTime('last_called');
            $table->dateTime('assignee_read_at')->nullable();
            $table->dateTime('give_up_at')->nullable();
            $table->string('appointment_label')->nullable();
            $table->string('contact_outcome')->nullable();
            $table->string('stage')->nullable();
            $table->string('assignee');
            $table->string('created_by')->nullable();
            $table->dateTime('delete_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duplicated_leads');
    }
};
