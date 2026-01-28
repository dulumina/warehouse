<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['sourceWarehouse', 'destinationWarehouse', 'items', 'user'])
            ->latest()
            ->paginate(15);
        return view('stock-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = \App\Models\Warehouse::all();
        $products = \App\Models\Product::all();
        return view('stock-transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'required|exists:warehouses,id|different:source_warehouse_id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable',
        ]);

        $transfer = StockTransfer::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'draft',
        ]);

        return redirect()->route('stock-transfers.show', $transfer)->with('success', 'Stock Transfer created successfully');
    }

    public function show(StockTransfer $transfer)
    {
        $transfer->load(['sourceWarehouse', 'destinationWarehouse', 'items', 'user']);
        return view('stock-transfers.show', compact('transfer'));
    }

    public function send(StockTransfer $transfer)
    {
        $this->authorize('update', $transfer);

        if ($transfer->status === 'draft') {
            $transfer->update(['status' => 'in_transit', 'sent_by' => auth()->id(), 'sent_at' => now()]);
        }

        return redirect()->back()->with('success', 'Stock Transfer sent');
    }

    public function receive(StockTransfer $transfer)
    {
        $this->authorize('update', $transfer);

        if ($transfer->status === 'in_transit') {
            $transfer->update(['status' => 'received', 'received_by' => auth()->id(), 'received_at' => now()]);
        }

        return redirect()->back()->with('success', 'Stock Transfer received');
    }

    public function destroy(StockTransfer $transfer)
    {
        if ($transfer->status === 'draft') {
            $transfer->delete();
            return redirect()->route('stock-transfers.index')->with('success', 'Stock Transfer deleted');
        }

        return redirect()->back()->with('error', 'Cannot delete in-transit or received stock transfer');
    }
}
