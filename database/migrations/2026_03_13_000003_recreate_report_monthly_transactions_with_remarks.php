<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('report_monthly_transactions');

        Schema::create('report_monthly_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_number_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('qty_budget')->nullable();
            $table->integer('qty_forecast')->nullable();
            $table->string('source_type')->default('budget');
            $table->string('remarks', 100);
            $table->string('month');
            $table->unsignedSmallInteger('year');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['part_number_id', 'month', 'year', 'remarks'], 'rmt_part_month_year_remarks_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_monthly_transactions');

        Schema::create('report_monthly_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_number_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('qty_budget')->nullable();
            $table->integer('qty_forecast')->nullable();
            $table->string('source_type')->default('budget');
            $table->string('month');
            $table->unsignedSmallInteger('year');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['part_number_id', 'month', 'year'], 'rmt_part_month_year_unique');
        });
    }
};
