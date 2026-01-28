<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_out_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('stock_out_id');
            $table->uuid('product_id');
            $table->uuid('location_id')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->decimal('quantity', 12, 4);
            $table->decimal('unit_cost', 14, 2);
            $table->decimal('subtotal', 14, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('stock_out_id')->references('id')->on('stock_outs')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->index(['stock_out_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_out_items');
    }
};
