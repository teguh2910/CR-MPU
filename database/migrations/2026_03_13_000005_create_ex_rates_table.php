<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ex_rates', function (Blueprint $table): void {
            $table->id();
            $table->string('currency', 20)->unique();
            $table->decimal('rate', 18, 6);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ex_rates');
    }
};
