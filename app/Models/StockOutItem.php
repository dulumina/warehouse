<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOutItem extends Model
{
    use HasUuids;

    protected $table = 'stock_out_items';

    protected $fillable = [
        'stock_out_id',
        'product_id',
        'location_id',
        'batch_number',
        'serial_number',
        'quantity',
        'unit_cost',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function stockOut(): BelongsTo
    {
        return $this->belongsTo(StockOut::class);
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
