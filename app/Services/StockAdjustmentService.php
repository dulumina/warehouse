<?php

namespace App\Services;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Product;
use App\Models\User;

class StockAdjustmentService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Create stock adjustment
     */
    public function create(array $data): StockAdjustment
    {
        $data['document_number'] = $this->generateDocumentNumber();
        $data['adjustment_date'] = $data['adjustment_date'] ?? now()->date();
        $data['status'] = 'DRAFT';

        return StockAdjustment::create($data);
    }

    /**
     * Add item to adjustment
     */
    public function addItem(StockAdjustment $adjustment, array $itemData): StockAdjustmentItem
    {
        $product = Product::find($itemData['product_id']);
        $inventory = $this->inventoryService->getOrCreateInventory(
            $product,
            $adjustment->warehouse,
            $itemData['location_id'] ? $product->warehouse->locations()->find($itemData['location_id']) : null,
            $itemData['batch_number'] ?? null
        );

        $itemData['system_quantity'] = $inventory->quantity;
        $itemData['difference'] = ($itemData['actual_quantity'] ?? 0) - $inventory->quantity;
        $itemData['unit_cost'] = $itemData['unit_cost'] ?? $inventory->unit_cost;
        $itemData['value_difference'] = $itemData['difference'] * $itemData['unit_cost'];

        return $adjustment->items()->create($itemData);
    }

    /**
     * Update adjustment item
     */
    public function updateItem(StockAdjustmentItem $item, array $data): StockAdjustmentItem
    {
        if (isset($data['actual_quantity'])) {
            $data['difference'] = $data['actual_quantity'] - $item->system_quantity;
            $data['value_difference'] = $data['difference'] * ($data['unit_cost'] ?? $item->unit_cost);
        }

        return $item->update($data) ? $item->fresh() : $item;
    }

    /**
     * Remove item from adjustment
     */
    public function removeItem(StockAdjustment $adjustment, StockAdjustmentItem $item): void
    {
        $item->delete();
    }

    /**
     * Approve adjustment and update inventory
     */
    public function approve(StockAdjustment $adjustment, User $user): void
    {
        if (!$adjustment->canBeApproved()) {
            throw new \Exception('Adjustment cannot be approved in ' . $adjustment->status . ' status');
        }

        // Update inventory for each item
        foreach ($adjustment->items as $item) {
            $inventory = $this->inventoryService->getOrCreateInventory(
                $item->product,
                $adjustment->warehouse,
                $item->location,
                $item->batch_number
            );

            // Update quantity based on difference
            if ($item->difference != 0) {
                if ($item->difference > 0) {
                    $this->inventoryService->addStock(
                        $item->product,
                        $adjustment->warehouse,
                        $item->difference,
                        $item->unit_cost,
                        $item->location,
                        $item->batch_number,
                        null,
                        $adjustment->document_number,
                        'ADJUSTMENT',
                        $user
                    );
                } else {
                    $this->inventoryService->reduceStock(
                        $item->product,
                        $adjustment->warehouse,
                        abs($item->difference),
                        $item->location,
                        $item->batch_number,
                        null,
                        $adjustment->document_number,
                        'ADJUSTMENT',
                        $user
                    );
                }
            }
        }

        $adjustment->approve($user);
    }

    /**
     * Reject adjustment
     */
    public function reject(StockAdjustment $adjustment, User $user): void
    {
        if (!$adjustment->canBeApproved()) {
            throw new \Exception('Adjustment cannot be rejected in ' . $adjustment->status . ' status');
        }

        $adjustment->reject($user);
    }

    /**
     * Generate unique document number
     */
    private function generateDocumentNumber(): string
    {
        $prefix = 'SA';
        $date = now()->format('Ymd');
        $sequence = StockAdjustment::whereDate('created_at', today())->count() + 1;

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }

    public function isDraft(StockAdjustment $adjustment): bool
    {
        return $adjustment->isDraft();
    }
}
