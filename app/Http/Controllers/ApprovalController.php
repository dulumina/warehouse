<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\StockTransfer;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function stockIns(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('summary')) {
                return response()->json([
                    'summary' => [
                        'pending' => StockIn::where('status', 'PENDING')->count(),
                        'approved_today' => StockIn::where('status', 'APPROVED')->whereDate('approved_at', today())->count(),
                        'total_value' => (float) StockIn::where('status', 'PENDING')->sum('total_value'),
                    ]
                ]);
            }
            return app(\App\Http\Controllers\StockInController::class)->datatables($request);
        }

        return view('approvals.stock-ins');
    }

    public function stockOuts(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('summary')) {
                return response()->json([
                    'summary' => [
                        'pending' => StockOut::where('status', 'PENDING')->count(),
                        'approved_today' => StockOut::where('status', 'APPROVED')->whereDate('approved_at', today())->count(),
                        'total_quantity' => (float) StockOut::where('status', 'PENDING')->sum('total_quantity'),
                    ]
                ]);
            }
            return app(\App\Http\Controllers\StockOutController::class)->datatables($request);
        }

        return view('approvals.stock-outs');
    }

    public function stockTransfers(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('summary')) {
                return response()->json([
                    'summary' => [
                        'draft' => StockTransfer::where('status', 'DRAFT')->count(),
                        'in_transit' => StockTransfer::where('status', 'IN_TRANSIT')->count(),
                        'received_today' => StockTransfer::where('status', 'RECEIVED')->whereDate('received_at', today())->count(),
                        'total_items' => (int) StockTransfer::whereIn('status', ['DRAFT', 'IN_TRANSIT'])->sum('total_items'),
                    ]
                ]);
            }
            return app(\App\Http\Controllers\StockTransferController::class)->datatables($request);
        }

        return view('approvals.stock-transfers');
    }

    public function stockAdjustments(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('summary')) {
                return response()->json([
                    'summary' => [
                        'pending' => StockAdjustment::where('status', 'DRAFT')->count(),
                        'approved_today' => StockAdjustment::where('status', 'APPROVED')->whereDate('approved_at', today())->count(),
                        'positive_adjustments' => StockAdjustmentItem::whereHas('stockAdjustment', function($q) {
                            $q->where('status', 'DRAFT');
                        })->where('difference', '>', 0)->count(),
                        'negative_adjustments' => StockAdjustmentItem::whereHas('stockAdjustment', function($q) {
                            $q->where('status', 'DRAFT');
                        })->where('difference', '<', 0)->count(),
                    ]
                ]);
            }
            return app(\App\Http\Controllers\StockAdjustmentController::class)->datatables($request);
        }

        return view('approvals.stock-adjustments');
    }
}
