<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('stock_adjustment_id');
            $table->uuid('product_id');
            $table->uuid('location_id')->nullable();
            $table->string('batch_number')->nullable();
            $table->decimal('system_quantity', 12, 4);
            $table->decimal('actual_quantity', 12, 4);
            $table->decimal('difference', 12, 4);
            $table->decimal('unit_cost', 14, 2);
            $table->decimal('value_difference', 14, 2);
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('stock_adjustment_id')->references('id')->on('stock_adjustments')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->index(['stock_adjustment_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
    }
};
