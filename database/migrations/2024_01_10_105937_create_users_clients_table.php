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
        Schema::create('users_clients', function (Blueprint $table) {
            $table->id();
            $table->string('username_and_site');
            $table->string('account_id');
            $table->string('full_name');
            $table->string('lead_status');
            $table->string('client_stage');
            $table->string('rank');
            $table->string('acc_manager');
            $table->string('kyc_status');
            $table->string('is_active');
            $table->string('staff_access');
            $table->string('crm_access');
            $table->string('lead_management_access');
            $table->string('timezone');
            $table->integer('user_orders_link');
            $table->string('view_on_site_link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_clients');
    }
};
