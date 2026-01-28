<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Batch;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        return view('inventory.index');
    }

    public function datatables(Request $request)
    {
        $draw = (int) $request->get('draw', 0);
        $start = max(0, (int) $request->get('start', 0));
        $length = (int) $request->get('length', 10);

        if ($length < 1) {
            $length = PHP_INT_MAX;
        }

        $search = $request->get('search')['value'] ?? '';
        $order = $request->get('order');

        $query = Inventory::with(['product', 'warehouse', 'location']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                })
                ->orWhereHas('warehouse', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        $totalRecords = Inventory::count();

        if ($order && is_array($order) && count($order) > 0) {
            $columnIndex = $order[0]['column'] ?? 0;
            $columns = $request->get('columns') ?? [];

            if (isset($columns[$columnIndex]['data'])) {
                $columnName = $columns[$columnIndex]['data'];
                $columnDir = strtoupper($order[0]['dir'] ?? 'ASC');

                if (in_array($columnName, ['quantity', 'reserved_quantity'])) {
                    $query->orderBy($columnName, $columnDir);
                } else {
                    $query->orderBy('created_at', 'DESC');
                }
            } else {
                $query->orderBy('created_at', 'DESC');
            }
        } else {
            $query->orderBy('created_at', 'DESC');
        }

        $filteredRecords = $query->count();

        $query->limit($length);
        if ($start > 0) {
            $query->offset($start);
        }
        $inventories = $query->get();

        $data = $inventories->map(function ($inventory) {
            return [
                'product' => $inventory->product?->name ?? '-',
                'warehouse' => $inventory->warehouse?->name ?? '-',
                'location' => $inventory->location?->name ?? '-',
                'quantity' => number_format($inventory->quantity, 0),
                'reserved_quantity' => number_format($inventory->reserved_quantity, 0),
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }


    public function lowStock()
    {
        $lowStock = Inventory::whereRaw('quantity < (SELECT min_stock FROM products WHERE products.id = inventory.product_id)')
            ->with(['product', 'warehouse'])
            ->paginate(15);
        return view('inventory.low-stock', compact('lowStock'));
    }

    public function expiring()
    {
        $expiringItems = Batch::where('expiry_date', '<', now()->addDays(30))
            ->with(['inventory', 'product'])
            ->paginate(15);
        return view('inventory.expiring', compact('expiringItems'));
    }
}
