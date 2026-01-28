<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentItem extends Model
{
    use HasUuids;

    protected $table = 'stock_adjustment_items';

    protected $fillable = [
        'stock_adjustment_id',
        'product_id',
        'location_id',
        'batch_number',
        'system_quantity',
        'actual_quantity',
        'difference',
        'unit_cost',
        'value_difference',
        'reason',
    ];

    protected $casts = [
        'system_quantity' => 'decimal:4',
        'actual_quantity' => 'decimal:4',
        'difference' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'value_difference' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function stockAdjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_id');
    }
}
