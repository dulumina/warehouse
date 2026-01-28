<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique(); // SKU
            $table->string('barcode')->unique()->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->uuid('category_id');
            $table->uuid('unit_id');
            $table->enum('type', ['FINISHED_GOOD', 'RAW_MATERIAL', 'CONSUMABLE'])->default('FINISHED_GOOD');
            $table->decimal('min_stock', 12, 4)->default(0);
            $table->decimal('max_stock', 12, 4)->default(0);
            $table->decimal('reorder_point', 12, 4)->default(0);
            $table->decimal('standard_cost', 14, 2)->default(0);
            $table->decimal('selling_price', 14, 2)->default(0);
            $table->decimal('weight', 12, 4)->nullable();
            $table->json('dimensions')->nullable(); // {length, width, height}
            $table->boolean('is_batch_tracked')->default(false);
            $table->boolean('is_serial_tracked')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->restrictOnDelete();
            $table->index('code');
            $table->index('barcode');
            $table->index(['category_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
