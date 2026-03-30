<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rm_suppliers', function (Blueprint $table): void {
            $table->date('period')->nullable()->after('part_no_id');
        });

        Schema::table('ex_rates', function (Blueprint $table): void {
            $table->date('period')->nullable()->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('rm_suppliers', function (Blueprint $table): void {
            $table->dropColumn('period');
        });

        Schema::table('ex_rates', function (Blueprint $table): void {
            $table->dropColumn('period');
        });
    }
};
