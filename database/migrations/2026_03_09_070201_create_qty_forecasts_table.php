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
        Schema::create('qty_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('update_qty_month_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('part_number_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('month');
            $table->integer('qty');
            $table->unsignedSmallInteger('year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qty_forecasts');
    }
};
