<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_monthly_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('update_qty_month_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('part_number_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('qty_budget')->nullable();
            $table->integer('qty_forecast')->nullable();
            $table->string('month');
            $table->unsignedSmallInteger('year');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['category_id', 'update_qty_month_id', 'part_number_id'], 'rmt_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_monthly_transactions');
    }
};
