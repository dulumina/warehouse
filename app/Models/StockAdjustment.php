<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'stock_adjustments';

    protected $fillable = [
        'document_number',
        'adjustment_date',
        'warehouse_id',
        'type',
        'status',
        'notes',
        'adjusted_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function adjustedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }

    // Alias for controller compatibility
    public function user(): BelongsTo
    {
        return $this->adjustedBy();
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
        return strtoupper($this->status) === 'DRAFT';
    }

    public function isDraft(): bool
    {
        return strtoupper($this->status) === 'DRAFT';
    }

    public function isApproved(): bool
    {
        return strtoupper($this->status) === 'APPROVED';
    }
}
