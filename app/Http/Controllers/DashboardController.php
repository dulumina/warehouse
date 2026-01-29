<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Batch;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_stock' => Inventory::sum('quantity'),
            'low_stock_count' => Inventory::whereRaw('quantity < (SELECT min_stock FROM products WHERE products.id = inventory.product_id)')->count(),
            'expiring_soon_count' => Batch::where('expiry_date', '>', now())
                ->where('expiry_date', '<', now()->addDays(30))
                ->count(),
            'total_warehouses' => Warehouse::count(),
            'inventory_value' => Inventory::join('products', 'inventory.product_id', '=', 'products.id')
                ->sum(DB::raw('inventory.quantity * products.standard_cost')),
        ];

        $recent_movements = StockMovement::with(['product', 'warehouse', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        $stock_by_category = DB::table('products')
            ->join('inventory', 'products.id', '=', 'inventory.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(inventory.quantity) as total_quantity'))
            ->groupBy('categories.name')
            ->get();

        $monthly_transactions = [
            'stock_in' => StockIn::where('status', 'APPROVED')
                ->whereMonth('created_at', now()->month)
                ->count(),
            'stock_out' => StockOut::where('status', 'APPROVED')
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        return view('dashboard', compact('stats', 'recent_movements', 'stock_by_category', 'monthly_transactions'));
    }
}
