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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('site');
            $table->string('username');
            $table->string('password');
            $table->string('account_id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('full_legal_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('country_of_citizenship')->nullable();
            $table->string('account_holder');
            $table->string('account_type');
            $table->string('customer_type')->nullable();
            $table->string('account_manager')->nullable();
            $table->string('lead_status')->nullable();
            $table->string('client_stage')->nullable();
            $table->string('rank');
            $table->text('remark')->nullable();
            $table->string('previous_broker_name')->nullable();
            $table->string('kyc_status')->nullable();
            $table->boolean('is_active');
            $table->boolean('has_crm_access');
            $table->boolean('has_leads_access');
            $table->boolean('is_staff');
            $table->boolean('is_superuser');
            $table->dateTime('last_login');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
