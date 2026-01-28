<?php

namespace App\Services;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Product;
use App\Models\User;

class StockTransferService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Create stock transfer
     */
    public function create(array $data): StockTransfer
    {
        $data['document_number'] = $this->generateDocumentNumber();
        $data['transaction_date'] = $data['transaction_date'] ?? now()->date();
        $data['status'] = 'DRAFT';

        return StockTransfer::create($data);
    }

    /**
     * Add item to transfer
     */
    public function addItem(StockTransfer $transfer, array $itemData): StockTransferItem
    {
        $item = $transfer->items()->create($itemData);
        $this->updateTotals($transfer);
        return $item;
    }

    /**
     * Remove item from transfer
     */
    public function removeItem(StockTransfer $transfer, StockTransferItem $item): void
    {
        $item->delete();
        $this->updateTotals($transfer);
    }

    /**
     * Update item
     */
    public function updateItem(StockTransferItem $item, array $data): StockTransferItem
    {
        $item->update($data);
        $this->updateTotals($item->stockTransfer);
        return $item;
    }

    /**
     * Send transfer and reduce stock from source warehouse
     */
    public function send(StockTransfer $transfer, User $user): void
    {
        if (!$transfer->isDraft()) {
            throw new \Exception('Only draft transfers can be sent');
        }

        // Reduce stock from source warehouse
        foreach ($transfer->items as $item) {
            $this->inventoryService->reduceStock(
                $item->product,
                $transfer->fromWarehouse,
                $item->quantity,
                $item->fromLocation,
                $item->batch_number,
                null,
                $transfer->document_number,
                'TRANSFER_OUT',
                $user
            );
        }

        $transfer->send($user);
    }

    /**
     * Receive transfer and add stock to destination warehouse
     */
    public function receive(StockTransfer $transfer, array $itemsReceived, User $user): void
    {
        if (!$transfer->isInTransit()) {
            throw new \Exception('Only in-transit transfers can be received');
        }

        // Add stock to destination warehouse
        foreach ($transfer->items as $item) {
            $quantityReceived = $itemsReceived[$item->id] ?? $item->quantity;

            $this->inventoryService->addStock(
                $item->product,
                $transfer->toWarehouse,
                $quantityReceived,
                0,
                $item->toLocation,
                $item->batch_number,
                null,
                $transfer->document_number,
                'TRANSFER_IN',
                $user
            );

            // Update received quantity
            $item->update(['quantity_received' => $quantityReceived]);
        }

        $transfer->receive($user);
    }

    /**
     * Update totals
     */
    private function updateTotals(StockTransfer $transfer): void
    {
        $items = $transfer->items()->get();

        $transfer->update([
            'total_items' => $items->count(),
            'total_quantity' => $items->sum('quantity'),
        ]);
    }

    /**
     * Generate unique document number
     */
    private function generateDocumentNumber(): string
    {
        $prefix = 'ST';
        $date = now()->format('Ymd');
        $sequence = StockTransfer::whereDate('created_at', today())->count() + 1;

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }

    public function isDraft(StockTransfer $transfer): bool
    {
        return $transfer->isDraft();
    }
}
