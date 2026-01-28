<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('batch_number')->unique();
            $table->date('manufacturing_date');
            $table->date('expiry_date');
            $table->uuid('supplier_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            $table->index('batch_number');
            $table->index(['product_id', 'expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
