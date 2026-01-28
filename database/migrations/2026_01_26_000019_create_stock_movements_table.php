<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('warehouse_id');
            $table->uuid('location_id')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->enum('transaction_type', ['STOCK_IN', 'STOCK_OUT', 'TRANSFER_IN', 'TRANSFER_OUT', 'ADJUSTMENT'])->default('STOCK_IN');
            $table->uuid('reference_id');
            $table->string('reference_number');
            $table->decimal('quantity', 12, 4);
            $table->decimal('balance_before', 12, 4);
            $table->decimal('balance_after', 12, 4);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
            $table->foreign('location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->index(['product_id', 'warehouse_id']);
            $table->index(['created_at', 'warehouse_id']);
            $table->index('reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
