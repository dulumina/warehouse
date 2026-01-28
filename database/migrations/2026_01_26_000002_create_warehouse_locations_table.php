<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('warehouse_id');
            $table->string('code');
            $table->string('name');
            $table->enum('zone', ['RECEIVING', 'STORAGE', 'PICKING', 'SHIPPING'])->default('STORAGE');
            $table->string('aisle')->nullable();
            $table->string('rack')->nullable();
            $table->string('level')->nullable();
            $table->string('bin')->nullable();
            $table->decimal('capacity', 12, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
            $table->unique(['warehouse_id', 'code']);
            $table->index(['warehouse_id', 'zone']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_locations');
    }
};
