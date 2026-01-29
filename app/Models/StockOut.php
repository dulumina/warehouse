<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOut extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'stock_outs';

    protected $fillable = [
        'document_number',
        'transaction_date',
        'warehouse_id',
        'customer_name',
        'reference_number',
        'type',
        'status',
        'total_items',
        'total_quantity',
        'total_value',
        'notes',
        'issued_by',
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

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOutItem::class);
    }

    // Alias for controller compatibility
    public function user(): BelongsTo
    {
        return $this->issuedBy();
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
        return strtoupper($this->status) === 'PENDING';
    }

    public function isPending(): bool
    {
        return strtoupper($this->status) === 'PENDING';
    }

    public function isApproved(): bool
    {
        return strtoupper($this->status) === 'APPROVED';
    }
}
