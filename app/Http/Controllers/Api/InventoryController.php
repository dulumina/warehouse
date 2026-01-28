<?php

namespace App\Http\Controllers\Api;

use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('view inventory');

        $query = Inventory::with(['product', 'warehouse', 'location']);

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $inventory = $query->paginate(15);

        return $this->success($inventory, 'Inventory retrieved successfully');
    }

    public function warehouseInventory(Warehouse $warehouse): JsonResponse
    {
        $this->authorize('view inventory');

        $inventory = $warehouse->inventory()
            ->with(['product', 'location'])
            ->get();

        $summary = [
            'total_items' => $inventory->count(),
            'total_quantity' => $inventory->sum('quantity'),
            'total_value' => $inventory->sum(function ($item) {
                return $item->quantity * $item->unit_cost;
            }),
        ];

        return $this->success([
            'summary' => $summary,
            'inventory' => $inventory,
        ], 'Warehouse inventory retrieved successfully');
    }

    public function productInventory(Product $product): JsonResponse
    {
        $this->authorize('view inventory');

        $inventory = $product->inventory()
            ->with('warehouse')
            ->get();

        return $this->success($inventory, 'Product inventory retrieved successfully');
    }

    public function lowStock(Request $request): JsonResponse
    {
        $this->authorize('view inventory');

        $warehouse_id = $request->warehouse_id;

        $inventory = Inventory::with(['product', 'warehouse'])
            ->whereHas('product', function ($q) {
                $q->whereColumn('quantity', '<', 'min_stock');
            });

        if ($warehouse_id) {
            $inventory->where('warehouse_id', $warehouse_id);
        }

        $results = $inventory->get();

        return $this->success($results, 'Low stock items retrieved successfully');
    }

    public function expiringItems(Request $request): JsonResponse
    {
        $this->authorize('view batch info');

        $days = $request->days ?? 30;
        $warehouse_id = $request->warehouse_id;

        $query = Inventory::with(['product', 'warehouse'])
            ->whereNotNull('batch_number');

        if ($warehouse_id) {
            $query->where('warehouse_id', $warehouse_id);
        }

        // This would need additional logic to check batch expiry dates
        // For now, return the structure
        $results = $query->get();

        return $this->success($results, 'Expiring items retrieved successfully');
    }
}
