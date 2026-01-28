<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasUuids;

    protected $table = 'stock_movements';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'location_id',
        'batch_number',
        'serial_number',
        'transaction_type',
        'reference_id',
        'reference_number',
        'quantity',
        'balance_before',
        'balance_after',
        'unit_cost',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'balance_before' => 'decimal:4',
        'balance_after' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Additional relationships for controller compatibility
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'product_id', 'product_id')
            ->where('warehouse_id', $this->warehouse_id);
    }

    public function user(): BelongsTo
    {
        return $this->createdBy();
    }
}
