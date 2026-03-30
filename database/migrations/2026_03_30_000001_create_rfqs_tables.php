<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfqs', function (Blueprint $table) {
            $table->id();
            $table->string('to_company');
            $table->string('to_pic');
            $table->date('rfq_date');
            $table->string('from_company');
            $table->string('from_department');
            $table->string('from_pic');
            $table->string('tel')->nullable();
            $table->string('email')->nullable();
            $table->string('model');
            $table->string('customer')->nullable();
            $table->string('product_name');
            $table->string('standard_qty');
            $table->string('drawing_timing')->nullable();
            $table->date('ots_target')->nullable();
            $table->date('otop_target')->nullable();
            $table->string('sop')->nullable();
            $table->text('target_note')->nullable();
            $table->date('quotation_due_date')->nullable();
            $table->string('delivery_location')->nullable();
            $table->string('price_incoterm')->nullable();
            $table->string('tooling_payment_method')->nullable();
            $table->string('raw_material_period')->nullable();
            $table->string('material_type')->nullable();
            $table->string('material_cps_price')->nullable();
            $table->string('exchange_period')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('rfq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
            $table->string('part_number');
            $table->string('part_name');
            $table->string('qty_mon');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('rfq_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
            $table->string('currency', 10);
            $table->string('rate');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_exchange_rates');
        Schema::dropIfExists('rfq_items');
        Schema::dropIfExists('rfqs');
    }
};
