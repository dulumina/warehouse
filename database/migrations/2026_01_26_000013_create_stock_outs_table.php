<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('document_number')->unique();
            $table->date('transaction_date');
            $table->uuid('warehouse_id');
            $table->string('customer_name')->nullable();
            $table->string('reference_number');
            $table->enum('type', ['SALES', 'RETURN', 'ADJUSTMENT', 'PRODUCTION', 'DAMAGED'])->default('SALES');
            $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'REJECTED', 'CANCELLED'])->default('DRAFT');
            $table->integer('total_items')->default(0);
            $table->decimal('total_quantity', 12, 4)->default(0);
            $table->decimal('total_value', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->uuid('issued_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->restrictOnDelete();
            $table->foreign('issued_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->index('document_number');
            $table->index(['warehouse_id', 'status']);
            $table->index(['status', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};
