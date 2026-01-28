<?php

namespace App\Services;

use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\WarehouseLocation;
use App\Models\User;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StockInService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Create stock in transaction
     */
    public function create(array $data): StockIn
    {
        $data['document_number'] = $this->generateDocumentNumber();
        $data['transaction_date'] = $data['transaction_date'] ?? now()->date();
        $data['status'] = 'DRAFT';

        return StockIn::create($data);
    }

    /**
     * Add item to stock in
     */
    public function addItem(StockIn $stockIn, array $itemData): StockInItem
    {
        $product = Product::find($itemData['product_id']);
        $itemData['unit_cost'] = $itemData['unit_cost'] ?? $product->standard_cost;
        $itemData['subtotal'] = $itemData['quantity'] * $itemData['unit_cost'];

        $item = $stockIn->items()->create($itemData);

        // Update totals
        $this->updateTotals($stockIn);

        return $item;
    }

    /**
     * Remove item from stock in
     */
    public function removeItem(StockIn $stockIn, StockInItem $item): void
    {
        $item->delete();
        $this->updateTotals($stockIn);
    }

    /**
     * Update item
     */
    public function updateItem(StockInItem $item, array $data): StockInItem
    {
        if (isset($data['quantity']) || isset($data['unit_cost'])) {
            $quantity = $data['quantity'] ?? $item->quantity;
            $unitCost = $data['unit_cost'] ?? $item->unit_cost;
            $data['subtotal'] = $quantity * $unitCost;
        }

        $item->update($data);
        $this->updateTotals($item->stockIn);

        return $item;
    }

    /**
     * Mark as pending (ready for approval)
     */
    public function markAsPending(StockIn $stockIn): void
    {
        if (!$stockIn->isDraft()) {
            throw new \Exception('Only draft stock in can be marked as pending');
        }

        $stockIn->update(['status' => 'PENDING']);
    }

    /**
     * Approve stock in and update inventory
     */
    public function approve(StockIn $stockIn, User $user): void
    {
        if (!$stockIn->canBeApproved()) {
            throw new \Exception('Stock in cannot be approved in ' . $stockIn->status . ' status');
        }

        // Update inventory for each item
        foreach ($stockIn->items as $item) {
            $this->inventoryService->addStock(
                $item->product,
                $stockIn->warehouse,
                $item->quantity,
                $item->unit_cost,
                $item->location,
                $item->batch_number,
                null,
                $stockIn->document_number,
                'STOCK_IN',
                $user
            );
        }

        $stockIn->approve($user);
    }

    /**
     * Reject stock in
     */
    public function reject(StockIn $stockIn, User $user): void
    {
        if (!$stockIn->canBeApproved()) {
            throw new \Exception('Stock in cannot be rejected in ' . $stockIn->status . ' status');
        }

        $stockIn->reject($user);
    }

    /**
     * Update totals
     */
    private function updateTotals(StockIn $stockIn): void
    {
        $items = $stockIn->items()->get();

        $stockIn->update([
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
        $prefix = 'SI';
        $date = now()->format('Ymd');
        $sequence = StockIn::whereDate('created_at', today())->count() + 1;

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }

    public function isDraft(StockIn $stockIn): bool
    {
        return $stockIn->status === 'DRAFT';
    }
}
