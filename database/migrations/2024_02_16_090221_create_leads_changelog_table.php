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
        Schema::create('leads_changelog', function (Blueprint $table) {
            $table->id();
            $table->string('lead_id');
            $table->string('column_name');
            $table->json('lead_changes')->nullable();
            $table->json('lead_front_changes')->nullable();
            $table->json('lead_notes_changes')->nullable();
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads_changelog');
    }
};
