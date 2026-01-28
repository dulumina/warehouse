<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('document_number')->unique();
            $table->date('transaction_date');
            $table->uuid('from_warehouse_id');
            $table->uuid('to_warehouse_id');
            $table->enum('status', ['DRAFT', 'IN_TRANSIT', 'RECEIVED', 'REJECTED', 'CANCELLED'])->default('DRAFT');
            $table->integer('total_items')->default(0);
            $table->decimal('total_quantity', 12, 4)->default(0);
            $table->text('notes')->nullable();
            $table->uuid('sent_by');
            $table->uuid('received_by')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->restrictOnDelete();
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->restrictOnDelete();
            $table->foreign('sent_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
            $table->index('document_number');
            $table->index(['status', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
