<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\StockTransfer;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;

class StockInController extends Controller
{
    public function index()
    {
        $stockIns = StockIn::with(['warehouse', 'supplier', 'items', 'user'])
            ->latest()
            ->paginate(15);
        return view('stock-ins.index', compact('stockIns'));
    }

    public function create()
    {
        $warehouses = \App\Models\Warehouse::all();
        $suppliers = \App\Models\Supplier::all();
        $products = \App\Models\Product::all();
        return view('stock-ins.create', compact('warehouses', 'suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'type' => 'required|in:purchase,return,adjustment',
            'transaction_date' => 'required|date',
            'notes' => 'nullable',
        ]);

        $stockIn = StockIn::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'draft',
        ]);

        return redirect()->route('stock-ins.show', $stockIn)->with('success', 'Stock In created successfully');
    }

    public function show(StockIn $stockIn)
    {
        $stockIn->load(['warehouse', 'supplier', 'items', 'user']);
        return view('stock-ins.show', compact('stockIn'));
    }

    public function pending(StockIn $stockIn)
    {
        $stockIn->update(['status' => 'pending']);
        return redirect()->back()->with('success', 'Stock In marked as pending');
    }

    public function approve(StockIn $stockIn)
    {
        $this->authorize('update', $stockIn);

        if ($stockIn->status === 'pending') {
            $stockIn->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        }

        return redirect()->back()->with('success', 'Stock In approved');
    }

    public function reject(StockIn $stockIn)
    {
        $this->authorize('update', $stockIn);

        if ($stockIn->status === 'pending') {
            $stockIn->update(['status' => 'rejected', 'rejected_by' => auth()->id(), 'rejected_at' => now()]);
        }

        return redirect()->back()->with('success', 'Stock In rejected');
    }

    public function destroy(StockIn $stockIn)
    {
        if ($stockIn->status === 'draft') {
            $stockIn->delete();
            return redirect()->route('stock-ins.index')->with('success', 'Stock In deleted');
        }

        return redirect()->back()->with('error', 'Cannot delete approved or pending stock in');
    }
}
