<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('stock_transfer_id');
            $table->uuid('product_id');
            $table->uuid('from_location_id')->nullable();
            $table->uuid('to_location_id')->nullable();
            $table->string('batch_number')->nullable();
            $table->decimal('quantity', 12, 4);
            $table->decimal('quantity_received', 12, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('stock_transfer_id')->references('id')->on('stock_transfers')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('from_location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->foreign('to_location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->index(['stock_transfer_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
    }
};
