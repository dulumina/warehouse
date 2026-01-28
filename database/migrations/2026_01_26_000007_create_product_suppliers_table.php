<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('supplier_id');
            $table->string('supplier_sku');
            $table->integer('lead_time_days')->default(0);
            $table->decimal('min_order_qty', 12, 4)->default(1);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->boolean('is_preferred')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->restrictOnDelete();
            $table->unique(['product_id', 'supplier_id']);
            $table->index('is_preferred');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_suppliers');
    }
};
