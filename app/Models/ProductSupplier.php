<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSupplier extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'product_suppliers';

    protected $fillable = [
        'product_id',
        'supplier_id',
        'supplier_sku',
        'lead_time_days',
        'min_order_qty',
        'unit_price',
        'is_preferred',
    ];

    protected $casts = [
        'lead_time_days' => 'integer',
        'min_order_qty' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'is_preferred' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
