<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('document_number')->unique();
            $table->date('adjustment_date');
            $table->uuid('warehouse_id');
            $table->enum('type', ['PHYSICAL_COUNT', 'CORRECTION', 'DAMAGED', 'EXPIRED', 'FOUND'])->default('PHYSICAL_COUNT');
            $table->enum('status', ['DRAFT', 'APPROVED', 'REJECTED'])->default('DRAFT');
            $table->text('notes')->nullable();
            $table->uuid('adjusted_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->restrictOnDelete();
            $table->foreign('adjusted_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->index('document_number');
            $table->index(['warehouse_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
