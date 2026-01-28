<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'stock_transfers';

    protected $fillable = [
        'document_number',
        'transaction_date',
        'from_warehouse_id',
        'to_warehouse_id',
        'status',
        'total_items',
        'total_quantity',
        'notes',
        'sent_by',
        'received_by',
        'sent_at',
        'received_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_items' => 'integer',
        'total_quantity' => 'decimal:4',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    // Aliases for controller compatibility
    public function sourceWarehouse(): BelongsTo
    {
        return $this->fromWarehouse();
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->toWarehouse();
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }

    // Alias for controller compatibility
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function send(User $user)
    {
        $this->update([
            'status' => 'IN_TRANSIT',
            'sent_by' => $user->id,
            'sent_at' => now(),
        ]);
    }

    public function receive(User $user)
    {
        $this->update([
            'status' => 'RECEIVED',
            'received_by' => $user->id,
            'received_at' => now(),
        ]);
    }

    public function isDraft(): bool
    {
        return $this->status === 'DRAFT';
    }

    public function isInTransit(): bool
    {
        return $this->status === 'IN_TRANSIT';
    }

    public function isReceived(): bool
    {
        return $this->status === 'RECEIVED';
    }
}
