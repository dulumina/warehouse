<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    public function index()
    {
        $stockOuts = StockOut::with(['warehouse', 'items', 'user'])
            ->latest()
            ->paginate(15);
        return view('stock-outs.index', compact('stockOuts'));
    }

    public function create()
    {
        $warehouses = \App\Models\Warehouse::all();
        $products = \App\Models\Product::all();
        return view('stock-outs.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:sales,internal,damage,loss',
            'transaction_date' => 'required|date',
            'notes' => 'nullable',
        ]);

        $stockOut = StockOut::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'draft',
        ]);

        return redirect()->route('stock-outs.show', $stockOut)->with('success', 'Stock Out created successfully');
    }

    public function show(StockOut $stockOut)
    {
        $stockOut->load(['warehouse', 'items', 'user']);
        return view('stock-outs.show', compact('stockOut'));
    }

    public function pending(StockOut $stockOut)
    {
        $stockOut->update(['status' => 'pending']);
        return redirect()->back()->with('success', 'Stock Out marked as pending');
    }

    public function approve(StockOut $stockOut)
    {
        $this->authorize('update', $stockOut);

        if ($stockOut->status === 'pending') {
            $stockOut->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        }

        return redirect()->back()->with('success', 'Stock Out approved');
    }

    public function reject(StockOut $stockOut)
    {
        $this->authorize('update', $stockOut);

        if ($stockOut->status === 'pending') {
            $stockOut->update(['status' => 'rejected', 'rejected_by' => auth()->id(), 'rejected_at' => now()]);
        }

        return redirect()->back()->with('success', 'Stock Out rejected');
    }

    public function destroy(StockOut $stockOut)
    {
        if ($stockOut->status === 'draft') {
            $stockOut->delete();
            return redirect()->route('stock-outs.index')->with('success', 'Stock Out deleted');
        }

        return redirect()->back()->with('error', 'Cannot delete approved or pending stock out');
    }
}
