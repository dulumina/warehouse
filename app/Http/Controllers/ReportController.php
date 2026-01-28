<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function stock()
    {
        $inventories = Inventory::with(['product', 'warehouse', 'location'])
            ->paginate(15);
        return view('reports.stock', compact('inventories'));
    }

    public function movements()
    {
        $movements = StockMovement::with(['inventory', 'product', 'warehouse', 'user'])
            ->latest()
            ->paginate(15);
        return view('reports.movements', compact('movements'));
    }

    public function valuation()
    {
        $valuations = Inventory::with(['product', 'warehouse'])
            ->get()
            ->map(function ($inventory) {
                return [
                    'product' => $inventory->product->name,
                    'warehouse' => $inventory->warehouse->name,
                    'quantity' => $inventory->quantity,
                    'unit_cost' => $inventory->unit_cost,
                    'total_value' => $inventory->quantity * $inventory->unit_cost,
                ];
            });

        return view('reports.valuation', compact('valuations'));
    }
}