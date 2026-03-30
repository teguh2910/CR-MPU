<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('process_cost_suppliers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('part_no_id')->constrained('part_numbers')->cascadeOnDelete();
            $table->decimal('process_cost_total', 18, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('process_cost_suppliers');
    }
};
