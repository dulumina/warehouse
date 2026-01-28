<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseLocation extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'warehouse_locations';

    protected $fillable = [
        'warehouse_id',
        'code',
        'name',
        'zone',
        'aisle',
        'rack',
        'level',
        'bin',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'decimal:4',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class, 'location_id');
    }

    public function stockInItems(): HasMany
    {
        return $this->hasMany(StockInItem::class, 'location_id');
    }

    public function stockOutItems(): HasMany
    {
        return $this->hasMany(StockOutItem::class, 'location_id');
    }

    public function serialNumbers(): HasMany
    {
        return $this->hasMany(SerialNumber::class, 'location_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'location_id');
    }
}
