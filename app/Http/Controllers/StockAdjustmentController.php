<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = StockAdjustment::with(['warehouse', 'items', 'user'])
            ->latest()
            ->paginate(15);
        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $warehouses = \App\Models\Warehouse::all();
        $products = \App\Models\Product::all();
        return view('stock-adjustments.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'adjustment_date' => 'required|date',
            'notes' => 'nullable',
        ]);

        $adjustment = StockAdjustment::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'draft',
        ]);

        return redirect()->route('stock-adjustments.show', $adjustment)->with('success', 'Stock Adjustment created successfully');
    }

    public function show(StockAdjustment $adjustment)
    {
        $adjustment->load(['warehouse', 'items', 'user']);
        return view('stock-adjustments.show', compact('adjustment'));
    }

    public function approve(StockAdjustment $adjustment)
    {
        $this->authorize('update', $adjustment);

        if ($adjustment->status === 'pending') {
            $adjustment->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        }

        return redirect()->back()->with('success', 'Stock Adjustment approved');
    }

    public function reject(StockAdjustment $adjustment)
    {
        $this->authorize('update', $adjustment);

        if ($adjustment->status === 'pending') {
            $adjustment->update(['status' => 'rejected', 'rejected_by' => auth()->id(), 'rejected_at' => now()]);
        }

        return redirect()->back()->with('success', 'Stock Adjustment rejected');
    }

    public function destroy(StockAdjustment $adjustment)
    {
        if ($adjustment->status === 'draft') {
            $adjustment->delete();
            return redirect()->route('stock-adjustments.index')->with('success', 'Stock Adjustment deleted');
        }

        return redirect()->back()->with('error', 'Cannot delete approved or pending stock adjustment');
    }
}
