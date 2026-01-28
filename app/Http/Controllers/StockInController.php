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
        return view('stock-ins.index');
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

        $query = StockIn::with(['warehouse', 'supplier']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                    ->orWhereHas('warehouse', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $totalRecords = StockIn::count();

        if ($order && is_array($order) && count($order) > 0) {
            $columnIndex = $order[0]['column'] ?? 0;
            $columns = $request->get('columns') ?? [];

            if (isset($columns[$columnIndex]['data'])) {
                $columnName = $columns[$columnIndex]['data'];
                $columnDir = strtoupper($order[0]['dir'] ?? 'ASC');

                if (in_array($columnName, ['document_number', 'status', 'transaction_date'])) {
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
        $stockIns = $query->get();

        $data = $stockIns->map(function ($stockIn) {
            $viewUrl = route('stock-ins.show', $stockIn);
            
            $actions = "<div class='flex justify-center gap-2'>" .
                "<a href='{$viewUrl}' class='btn btn-sm btn-info' title='View'><i class='ti ti-eye'></i></a>";

            if ($stockIn->status === 'draft') {
                $deleteUrl = route('stock-ins.destroy', $stockIn);
                $actions .= "<form action='{$deleteUrl}' method='POST' class='inline' onsubmit='return confirm(\"Delete this stock in?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<input type='hidden' name='_method' value='DELETE'>" .
                    "<button type='submit' class='btn btn-sm btn-error' title='Delete'><i class='ti ti-trash'></i></button>" .
                    "</form>";
            }
            
            $actions .= "</div>";

            $badgeClass = match($stockIn->status) {
                'approved' => 'badge-success',
                'rejected' => 'badge-error',
                'pending' => 'badge-warning',
                default => 'badge-ghost'
            };

            return [
                'document_number' => "<span class='font-mono text-blue-600'>{$stockIn->document_number}</span>",
                'warehouse' => $stockIn->warehouse?->name ?? '-',
                'supplier' => $stockIn->supplier?->name ?? '-',
                'status' => "<span class='badge {$badgeClass} gap-2'>{$stockIn->status}</span>",
                'transaction_date' => $stockIn->transaction_date?->format('Y-m-d'),
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
