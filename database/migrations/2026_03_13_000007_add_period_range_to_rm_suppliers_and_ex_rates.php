<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rm_suppliers', function (Blueprint $table): void {
            $table->date('period_from')->nullable()->after('part_no_id');
            $table->date('period_to')->nullable()->after('period_from');
        });

        Schema::table('ex_rates', function (Blueprint $table): void {
            $table->date('period_from')->nullable()->after('currency');
            $table->date('period_to')->nullable()->after('period_from');
        });

        DB::table('rm_suppliers')
            ->whereNotNull('period')
            ->update([
                'period_from' => DB::raw('period'),
                'period_to' => DB::raw('period'),
            ]);

        DB::table('ex_rates')
            ->whereNotNull('period')
            ->update([
                'period_from' => DB::raw('period'),
                'period_to' => DB::raw('period'),
            ]);
    }

    public function down(): void
    {
        Schema::table('rm_suppliers', function (Blueprint $table): void {
            $table->dropColumn(['period_from', 'period_to']);
        });

        Schema::table('ex_rates', function (Blueprint $table): void {
            $table->dropColumn(['period_from', 'period_to']);
        });
    }
};
