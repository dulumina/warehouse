<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockIn extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'stock_ins';

    protected $fillable = [
        'document_number',
        'transaction_date',
        'warehouse_id',
        'supplier_id',
        'reference_number',
        'type',
        'status',
        'total_items',
        'total_quantity',
        'total_value',
        'notes',
        'received_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_items' => 'integer',
        'total_quantity' => 'decimal:4',
        'total_value' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockInItem::class);
    }

    public function approve(User $user)
    {
        $this->update([
            'status' => 'APPROVED',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
    }

    public function reject(User $user)
    {
        $this->update([
            'status' => 'REJECTED',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'PENDING';
    }

    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }
}
