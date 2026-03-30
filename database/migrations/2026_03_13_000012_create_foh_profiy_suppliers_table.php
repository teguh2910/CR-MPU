<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('foh_profiy_suppliers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('part_no_id')->constrained('part_numbers')->cascadeOnDelete();
            $table->decimal('percentage', 8, 4);
            $table->string('remarks', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foh_profiy_suppliers');
    }
};
