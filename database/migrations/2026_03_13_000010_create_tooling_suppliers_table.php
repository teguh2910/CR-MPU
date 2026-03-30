<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tooling_suppliers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('part_no_id')->constrained('part_numbers')->cascadeOnDelete();
            $table->decimal('tooling_price', 18, 4);
            $table->decimal('depre_per_pcs', 18, 6);
            $table->string('status', 30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tooling_suppliers');
    }
};
