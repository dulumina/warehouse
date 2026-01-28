<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Batch;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with(['product', 'warehouse', 'location'])
            ->paginate(15);
        return view('inventory.index', compact('inventories'));
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
