<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serial_numbers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('serial_number')->unique();
            $table->uuid('batch_id')->nullable();
            $table->enum('status', ['AVAILABLE', 'RESERVED', 'SOLD', 'DAMAGED'])->default('AVAILABLE');
            $table->uuid('warehouse_id')->nullable();
            $table->uuid('location_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('batch_id')->references('id')->on('batches')->nullOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
            $table->foreign('location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->index('serial_number');
            $table->index(['product_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serial_numbers');
    }
};
