<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rm_suppliers', function (Blueprint $table): void {
            $table->decimal('rm_weight_gram', 18, 4)->nullable()->after('rm_basis_price');
        });
    }

    public function down(): void
    {
        Schema::table('rm_suppliers', function (Blueprint $table): void {
            $table->dropColumn('rm_weight_gram');
        });
    }
};
