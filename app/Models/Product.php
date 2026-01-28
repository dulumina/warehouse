<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'code',
        'barcode',
        'name',
        'description',
        'category_id',
        'unit_id',
        'type',
        'min_stock',
        'max_stock',
        'reorder_point',
        'standard_cost',
        'selling_price',
        'weight',
        'dimensions',
        'is_batch_tracked',
        'is_serial_tracked',
        'is_active',
    ];

    protected $casts = [
        'min_stock' => 'decimal:4',
        'max_stock' => 'decimal:4',
        'reorder_point' => 'decimal:4',
        'standard_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'weight' => 'decimal:4',
        'dimensions' => 'json',
        'is_batch_tracked' => 'boolean',
        'is_serial_tracked' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(ProductSupplier::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function serialNumbers(): HasMany
    {
        return $this->hasMany(SerialNumber::class);
    }

    public function stockInItems(): HasMany
    {
        return $this->hasMany(StockInItem::class);
    }

    public function stockOutItems(): HasMany
    {
        return $this->hasMany(StockOutItem::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getTotalStockAttribute(): float
    {
        return $this->inventory()->sum('quantity');
    }

    public function getAvailableStockAttribute(): float
    {
        return $this->inventory()
            ->selectRaw('SUM(quantity - reserved_quantity) as total')
            ->first()
            ?->total ?? 0;
    }
}
