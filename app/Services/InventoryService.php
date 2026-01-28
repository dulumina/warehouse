<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class InventoryService
{
    /**
     * Get or create inventory record
     */
    public function getOrCreateInventory(
        Product $product,
        Warehouse $warehouse,
        ?WarehouseLocation $location = null,
        ?string $batchNumber = null,
        ?string $serialNumber = null
    ): Inventory {
        return Inventory::firstOrCreate(
            [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'location_id' => $location?->id,
                'batch_number' => $batchNumber,
                'serial_number' => $serialNumber,
            ],
            [
                'quantity' => 0,
                'reserved_quantity' => 0,
                'unit_cost' => $product->standard_cost,
            ]
        );
    }

    /**
     * Add stock to inventory
     */
    public function addStock(
        Product $product,
        Warehouse $warehouse,
        float $quantity,
        float $unitCost = 0,
        ?WarehouseLocation $location = null,
        ?string $batchNumber = null,
        ?string $serialNumber = null,
        string $referenceNumber = '',
        string $transactionType = 'STOCK_IN',
        User $user
    ): Inventory {
        $inventory = $this->getOrCreateInventory($product, $warehouse, $location, $batchNumber, $serialNumber);

        $balanceBefore = $inventory->quantity;

        $inventory->update([
            'quantity' => $inventory->quantity + $quantity,
            'unit_cost' => $unitCost ?: $inventory->unit_cost,
            'last_stock_in' => now(),
        ]);

        // Log movement
        $this->logMovement(
            $product,
            $warehouse,
            $location,
            $batchNumber,
            $serialNumber,
            $transactionType,
            $product->id,
            $referenceNumber,
            $quantity,
            $balanceBefore,
            $inventory->quantity,
            $unitCost ?: $inventory->unit_cost,
            $user
        );

        return $inventory;
    }

    /**
     * Reduce stock from inventory
     */
    public function reduceStock(
        Product $product,
        Warehouse $warehouse,
        float $quantity,
        ?WarehouseLocation $location = null,
        ?string $batchNumber = null,
        ?string $serialNumber = null,
        string $referenceNumber = '',
        string $transactionType = 'STOCK_OUT',
        User $user
    ): Inventory {
        $inventory = $this->getOrCreateInventory($product, $warehouse, $location, $batchNumber, $serialNumber);

        $balanceBefore = $inventory->quantity;

        $inventory->update([
            'quantity' => max(0, $inventory->quantity - $quantity),
            'last_stock_out' => now(),
        ]);

        // Log movement
        $this->logMovement(
            $product,
            $warehouse,
            $location,
            $batchNumber,
            $serialNumber,
            $transactionType,
            $product->id,
            $referenceNumber,
            -$quantity,
            $balanceBefore,
            $inventory->quantity,
            $inventory->unit_cost,
            $user
        );

        return $inventory;
    }

    /**
     * Reserve stock
     */
    public function reserveStock(
        Product $product,
        Warehouse $warehouse,
        float $quantity,
        ?WarehouseLocation $location = null,
        ?string $batchNumber = null,
        ?string $serialNumber = null
    ): bool {
        $inventory = $this->getOrCreateInventory($product, $warehouse, $location, $batchNumber, $serialNumber);

        if ($inventory->available_quantity < $quantity) {
            return false;
        }

        $inventory->update([
            'reserved_quantity' => $inventory->reserved_quantity + $quantity,
        ]);

        return true;
    }

    /**
     * Release reserved stock
     */
    public function releaseReservedStock(
        Product $product,
        Warehouse $warehouse,
        float $quantity,
        ?WarehouseLocation $location = null,
        ?string $batchNumber = null,
        ?string $serialNumber = null
    ): bool {
        $inventory = $this->getOrCreateInventory($product, $warehouse, $location, $batchNumber, $serialNumber);

        if ($inventory->reserved_quantity < $quantity) {
            return false;
        }

        $inventory->update([
            'reserved_quantity' => $inventory->reserved_quantity - $quantity,
        ]);

        return true;
    }

    /**
     * Get low stock items in warehouse
     */
    public function getLowStockItems(Warehouse $warehouse): Collection
    {
        return $warehouse->inventory()
            ->whereHas('product', function ($query) {
                $query->whereColumn('inventory.quantity', '<', 'products.min_stock');
            })
            ->with('product')
            ->get();
    }

    /**
     * Get expiring items
     */
    public function getExpiringItems(Warehouse $warehouse, int $days = 30): Collection
    {
        return $warehouse->inventory()
            ->whereNotNull('batch_number')
            ->with(['product'])
            ->get()
            ->filter(function ($inventory) use ($days) {
                if ($inventory->batch_number) {
                    $batch = $inventory->product->batches()
                        ->where('batch_number', $inventory->batch_number)
                        ->first();

                    return $batch && $batch->isExpiringSoon($days);
                }
                return false;
            });
    }

    /**
     * Log stock movement
     */
    private function logMovement(
        Product $product,
        Warehouse $warehouse,
        ?WarehouseLocation $location,
        ?string $batchNumber,
        ?string $serialNumber,
        string $transactionType,
        string $referenceId,
        string $referenceNumber,
        float $quantity,
        float $balanceBefore,
        float $balanceAfter,
        float $unitCost,
        User $user
    ): StockMovement {
        return StockMovement::create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'location_id' => $location?->id,
            'batch_number' => $batchNumber,
            'serial_number' => $serialNumber,
            'transaction_type' => $transactionType,
            'reference_id' => $referenceId,
            'reference_number' => $referenceNumber,
            'quantity' => $quantity,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'unit_cost' => $unitCost,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Get inventory summary for warehouse
     */
    public function getWarehouseInventorySummary(Warehouse $warehouse): array
    {
        $inventory = $warehouse->inventory()
            ->with('product')
            ->get();

        return [
            'total_items' => $inventory->count(),
            'total_quantity' => $inventory->sum('quantity'),
            'total_value' => $inventory->sum(function ($item) {
                return $item->quantity * $item->unit_cost;
            }),
            'inventory' => $inventory,
        ];
    }

    /**
     * Get product inventory across all warehouses
     */
    public function getProductInventory(Product $product): Collection
    {
        return $product->inventory()
            ->with('warehouse')
            ->get();
    }
}
