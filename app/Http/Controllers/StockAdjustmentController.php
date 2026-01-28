<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        return view('stock-adjustments.index');
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

        $query = StockAdjustment::with(['warehouse']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                    ->orWhereHas('warehouse', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $totalRecords = StockAdjustment::count();

        if ($order && is_array($order) && count($order) > 0) {
            $columnIndex = $order[0]['column'] ?? 0;
            $columns = $request->get('columns') ?? [];

            if (isset($columns[$columnIndex]['data'])) {
                $columnName = $columns[$columnIndex]['data'];
                $columnDir = strtoupper($order[0]['dir'] ?? 'ASC');

                if (in_array($columnName, ['document_number', 'type', 'status', 'adjustment_date'])) {
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
        $adjustments = $query->get();

        $data = $adjustments->map(function ($adjustment) {
            $viewUrl = route('stock-adjustments.show', $adjustment);
            
            $actions = "<div class='flex justify-center gap-2'>" .
                "<a href='{$viewUrl}' class='btn btn-sm btn-info' title='View'><i class='ti ti-eye'></i></a>";

            if ($adjustment->status === 'draft') {
                $deleteUrl = route('stock-adjustments.destroy', $adjustment);
                $actions .= "<form action='{$deleteUrl}' method='POST' class='inline' onsubmit='return confirm(\"Delete this adjustment?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<input type='hidden' name='_method' value='DELETE'>" .
                    "<button type='submit' class='btn btn-sm btn-error' title='Delete'><i class='ti ti-trash'></i></button>" .
                    "</form>";
            }
            
            $actions .= "</div>";

            $badgeClass = match($adjustment->status) {
                'approved' => 'badge-success',
                'rejected' => 'badge-error',
                'pending' => 'badge-warning',
                default => 'badge-ghost'
            };

            return [
                'document_number' => "<span class='font-mono text-blue-600'>{$adjustment->document_number}</span>",
                'warehouse' => $adjustment->warehouse?->name ?? '-',
                'type' => ucfirst($adjustment->type),
                'status' => "<span class='badge {$badgeClass} gap-2'>{$adjustment->status}</span>",
                'adjustment_date' => $adjustment->adjustment_date?->format('Y-m-d'),
                'actions' => $actions,
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
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
