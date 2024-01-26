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
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();
            $table->string('id_and_site');
            $table->string('vc');
            $table->string('registered_name');
            $table->string('ao_numbers');
            $table->float('balance_due', 8, 2);
            $table->string('docs_received');
            $table->date('tc_sent');
            $table->date('tt_received');
            $table->date('written_date');
            $table->string('view_on_site_link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_orders');
    }
};
