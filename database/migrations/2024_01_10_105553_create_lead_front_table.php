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
        Schema::create('lead_front', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('assignee');
            $table->string('product');
            $table->float('quantity', 8, 2);
            $table->float('price', 8, 2);
            $table->float('total', 8, 2);
            $table->float('commission', 8, 2);
            $table->string('vc');
            $table->boolean('sdm');
            $table->boolean('liquid');
            $table->text('mimo');
            $table->string('bank_name');
            $table->integer('bank_account');
            $table->text('note');
            $table->string('linked_lead');
            $table->dateTime('edited_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_front');
    }
};
