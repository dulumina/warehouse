<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('warehouse_id');
            $table->uuid('location_id')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->decimal('quantity', 12, 4)->default(0);
            $table->decimal('reserved_quantity', 12, 4)->default(0);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->timestamp('last_stock_in')->nullable();
            $table->timestamp('last_stock_out')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
            $table->foreign('location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->unique(['product_id', 'warehouse_id', 'location_id', 'batch_number', 'serial_number'], 'inv_unique');
            $table->index(['warehouse_id', 'quantity']);
            $table->index(['product_id', 'quantity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
