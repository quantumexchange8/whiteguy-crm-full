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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('trade_id');
            $table->date('date');
            $table->string('action_type');
            $table->string('stock_type');
            $table->string('stock');
            $table->float('unit_price', 8, 2);
            $table->float('quantity', 8, 2);
            $table->float('total_price', 8, 2);
            $table->float('profit', 8, 2);
            $table->string('status');
            $table->dateTime('confirmed_at');
            $table->string('limb_stage');
            $table->string('user_link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
