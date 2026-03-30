<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('other_cost_suppliers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('part_no_id')->constrained('part_numbers')->cascadeOnDelete();
            $table->string('remark', 255);
            $table->decimal('cost', 18, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('other_cost_suppliers');
    }
};
