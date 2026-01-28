<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasUuids;

    protected $table = 'inventory';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'location_id',
        'batch_number',
        'serial_number',
        'quantity',
        'reserved_quantity',
        'unit_cost',
        'last_stock_in',
        'last_stock_out',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'reserved_quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'last_stock_in' => 'datetime',
        'last_stock_out' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function getAvailableQuantityAttribute(): float
    {
        return max(0, (float)$this->quantity - (float)$this->reserved_quantity);
    }
}
