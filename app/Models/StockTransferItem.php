<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferItem extends Model
{
    use HasUuids;

    protected $table = 'stock_transfer_items';

    protected $fillable = [
        'stock_transfer_id',
        'product_id',
        'from_location_id',
        'to_location_id',
        'batch_number',
        'quantity',
        'quantity_received',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'quantity_received' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function stockTransfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'to_location_id');
    }
}
