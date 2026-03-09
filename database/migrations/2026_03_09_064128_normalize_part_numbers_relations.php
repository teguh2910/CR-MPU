<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('part_numbers', function (Blueprint $table) {
            if (! Schema::hasColumn('part_numbers', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('part_name')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            }

            if (! Schema::hasColumn('part_numbers', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('supplier_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            }

            if (! Schema::hasColumn('part_numbers', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('product_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            }
        });

        $suppliers = DB::table('part_numbers')
            ->whereNotNull('supplier')
            ->select('supplier')
            ->distinct()
            ->pluck('supplier');

        foreach ($suppliers as $name) {
            DB::table('suppliers')->updateOrInsert(['name' => $name], ['name' => $name]);
        }

        $products = DB::table('part_numbers')
            ->whereNotNull('product')
            ->select('product')
            ->distinct()
            ->pluck('product');

        foreach ($products as $name) {
            DB::table('products')->updateOrInsert(['name' => $name], ['name' => $name]);
        }

        $categories = DB::table('part_numbers')
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->pluck('category');

        foreach ($categories as $name) {
            DB::table('categories')->updateOrInsert(['name' => $name], ['name' => $name]);
        }

        $supplierMap = DB::table('suppliers')->pluck('id', 'name');
        $productMap = DB::table('products')->pluck('id', 'name');
        $categoryMap = DB::table('categories')->pluck('id', 'name');

        if (
            Schema::hasColumn('part_numbers', 'supplier')
            && Schema::hasColumn('part_numbers', 'product')
            && Schema::hasColumn('part_numbers', 'category')
        ) {
            $partNumbers = DB::table('part_numbers')
                ->select('id', 'supplier', 'product', 'category')
                ->get();

            foreach ($partNumbers as $partNumber) {
                DB::table('part_numbers')
                    ->where('id', $partNumber->id)
                    ->update([
                        'supplier_id' => $supplierMap[$partNumber->supplier] ?? null,
                        'product_id' => $productMap[$partNumber->product] ?? null,
                        'category_id' => $categoryMap[$partNumber->category] ?? null,
                    ]);
            }
        }

        Schema::table('part_numbers', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('part_numbers', 'supplier')) {
                $columnsToDrop[] = 'supplier';
            }

            if (Schema::hasColumn('part_numbers', 'product')) {
                $columnsToDrop[] = 'product';
            }

            if (Schema::hasColumn('part_numbers', 'category')) {
                $columnsToDrop[] = 'category';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('part_numbers', function (Blueprint $table) {
            if (! Schema::hasColumn('part_numbers', 'supplier')) {
                $table->string('supplier')->nullable();
            }

            if (! Schema::hasColumn('part_numbers', 'product')) {
                $table->string('product')->nullable();
            }

            if (! Schema::hasColumn('part_numbers', 'category')) {
                $table->string('category')->nullable();
            }
        });

        $supplierNames = DB::table('suppliers')->pluck('name', 'id');
        $productNames = DB::table('products')->pluck('name', 'id');
        $categoryNames = DB::table('categories')->pluck('name', 'id');

        $partNumbers = DB::table('part_numbers')
            ->select('id', 'supplier_id', 'product_id', 'category_id')
            ->get();

        foreach ($partNumbers as $partNumber) {
            DB::table('part_numbers')
                ->where('id', $partNumber->id)
                ->update([
                    'supplier' => $supplierNames[$partNumber->supplier_id] ?? null,
                    'product' => $productNames[$partNumber->product_id] ?? null,
                    'category' => $categoryNames[$partNumber->category_id] ?? null,
                ]);
        }

        Schema::table('part_numbers', function (Blueprint $table) {
            if (Schema::hasColumn('part_numbers', 'supplier_id')) {
                $table->dropConstrainedForeignId('supplier_id');
            }

            if (Schema::hasColumn('part_numbers', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }

            if (Schema::hasColumn('part_numbers', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });
    }
};
