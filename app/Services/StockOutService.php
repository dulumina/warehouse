<?php

namespace App\Services;

use App\Models\StockOut;
use App\Models\StockOutItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\User;

class StockOutService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Create stock out transaction
     */
    public function create(array $data): StockOut
    {
        $data['document_number'] = $this->generateDocumentNumber();
        $data['transaction_date'] = $data['transaction_date'] ?? now()->date();
        $data['status'] = 'DRAFT';

        return StockOut::create($data);
    }

    /**
     * Add item to stock out
     */
    public function addItem(StockOut $stockOut, array $itemData): StockOutItem
    {
        $product = Product::find($itemData['product_id']);
        $itemData['unit_cost'] = $itemData['unit_cost'] ?? $product->standard_cost;
        $itemData['subtotal'] = $itemData['quantity'] * $itemData['unit_cost'];

        $item = $stockOut->items()->create($itemData);

        // Update totals
        $this->updateTotals($stockOut);

        return $item;
    }

    /**
     * Remove item from stock out
     */
    public function removeItem(StockOut $stockOut, StockOutItem $item): void
    {
        $item->delete();
        $this->updateTotals($stockOut);
    }

    /**
     * Update item
     */
    public function updateItem(StockOutItem $item, array $data): StockOutItem
    {
        if (isset($data['quantity']) || isset($data['unit_cost'])) {
            $quantity = $data['quantity'] ?? $item->quantity;
            $unitCost = $data['unit_cost'] ?? $item->unit_cost;
            $data['subtotal'] = $quantity * $unitCost;
        }

        $item->update($data);
        $this->updateTotals($item->stockOut);

        return $item;
    }

    /**
     * Mark as pending (ready for approval)
     */
    public function markAsPending(StockOut $stockOut): void
    {
        if (!$stockOut->isDraft()) {
            throw new \Exception('Only draft stock out can be marked as pending');
        }

        $stockOut->update(['status' => 'PENDING']);
    }

    /**
     * Approve stock out and update inventory
     */
    public function approve(StockOut $stockOut, User $user): void
    {
        if (!$stockOut->canBeApproved()) {
            throw new \Exception('Stock out cannot be approved in ' . $stockOut->status . ' status');
        }

        // Update inventory for each item
        foreach ($stockOut->items as $item) {
            $this->inventoryService->reduceStock(
                $item->product,
                $stockOut->warehouse,
                $item->quantity,
                $item->location,
                $item->batch_number,
                $item->serial_number,
                $stockOut->document_number,
                'STOCK_OUT',
                $user
            );
        }

        $stockOut->approve($user);
    }

    /**
     * Reject stock out
     */
    public function reject(StockOut $stockOut, User $user): void
    {
        if (!$stockOut->canBeApproved()) {
            throw new \Exception('Stock out cannot be rejected in ' . $stockOut->status . ' status');
        }

        $stockOut->reject($user);
    }

    /**
     * Update totals
     */
    private function updateTotals(StockOut $stockOut): void
    {
        $items = $stockOut->items()->get();

        $stockOut->update([
            'total_items' => $items->count(),
            'total_quantity' => $items->sum('quantity'),
            'total_value' => $items->sum('subtotal'),
        ]);
    }

    /**
     * Generate unique document number
     */
    private function generateDocumentNumber(): string
    {
        $prefix = 'SO';
        $date = now()->format('Ymd');
        $sequence = StockOut::whereDate('created_at', today())->count() + 1;

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }

    public function isDraft(StockOut $stockOut): bool
    {
        return $stockOut->status === 'DRAFT';
    }
}
