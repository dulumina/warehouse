<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\StockTransfer;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function stockIns()
    {
        $pendingStockIns = StockIn::where('status', 'pending')
            ->with(['warehouse', 'supplier', 'user'])
            ->paginate(15);
        return view('approvals.stock-ins', compact('pendingStockIns'));
    }

    public function stockOuts()
    {
        $pendingStockOuts = StockOut::where('status', 'pending')
            ->with(['warehouse', 'user'])
            ->paginate(15);
        return view('approvals.stock-outs', compact('pendingStockOuts'));
    }

    public function stockTransfers()
    {
        $pendingTransfers = StockTransfer::where('status', 'draft')
            ->with(['sourceWarehouse', 'destinationWarehouse', 'user'])
            ->paginate(15);
        return view('approvals.stock-transfers', compact('pendingTransfers'));
    }

    public function stockAdjustments()
    {
        $pendingAdjustments = StockAdjustment::where('status', 'pending')
            ->with(['warehouse', 'user'])
            ->paginate(15);
        return view('approvals.stock-adjustments', compact('pendingAdjustments'));
    }
}
