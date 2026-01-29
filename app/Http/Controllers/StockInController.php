<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Services\StockInService;
use Illuminate\Http\Request;

class StockInController extends Controller
{
    public function __construct(protected StockInService $stockInService)
    {
    }

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
            $length = 10;
        }

        $search = $request->get('search')['value'] ?? '';
        $order = $request->get('order');

        $query = StockIn::with(['warehouse', 'supplier']);

        if ($request->has('status')) {
            $query->where('status', strtoupper($request->status));
        }

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
        $filteredRecords = $query->count();

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

        $stockIns = $query->skip($start)->take($length)->get();

        $data = $stockIns->map(function ($stockIn) {
            $viewUrl = route('stock-ins.show', $stockIn);
            
            $actions = "<div class='flex justify-center gap-2'>" .
                "<a href='{$viewUrl}' class='btn btn-sm btn-info' title='View'><i class='ti ti-eye'></i></a>";

            if (strtoupper($stockIn->status) === 'DRAFT') {
                $deleteUrl = route('stock-ins.destroy', $stockIn);
                $actions .= "<form action='{$deleteUrl}' method='POST' class='inline' onsubmit='return confirm(\"Delete this stock in?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<input type='hidden' name='_method' value='DELETE'>" .
                    "<button type='submit' class='btn btn-sm btn-error' title='Delete'><i class='ti ti-trash'></i></button>" .
                    "</form>";
            }

            if (strtoupper($stockIn->status) === 'PENDING') {
                $approveUrl = route('stock-ins.approve', $stockIn);
                $rejectUrl = route('stock-ins.reject', $stockIn);
                $actions .= "<form action='{$approveUrl}' method='POST' class='inline' onsubmit='return confirm(\"Approve this stock in?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<button type='submit' class='btn btn-sm btn-success' title='Approve'><i class='ti ti-check'></i></button>" .
                    "</form>";
                $actions .= "<form action='{$rejectUrl}' method='POST' class='inline' onsubmit='return confirm(\"Reject this stock in?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<button type='submit' class='btn btn-sm btn-error' title='Reject'><i class='ti ti-x'></i></button>" .
                    "</form>";
            }
            
            $actions .= "</div>";

            $status = strtoupper($stockIn->status);
            $badgeClass = match($status) {
                'APPROVED' => 'badge-success',
                'REJECTED' => 'badge-error',
                'PENDING'  => 'badge-warning',
                'DRAFT'    => 'badge-ghost',
                default    => 'badge-ghost'
            };

            return [
                'document_number' => "<span class='font-mono text-blue-600'>{$stockIn->document_number}</span>",
                'warehouse' => $stockIn->warehouse?->name ?? '-',
                'supplier' => $stockIn->supplier?->name ?? '-',
                'type' => ucfirst(strtolower($stockIn->type ?? 'PURCHASE')),
                'status' => "<span class='badge {$badgeClass} gap-2'>{$status}</span>",
                'transaction_date' => $stockIn->transaction_date ? $stockIn->transaction_date->format('Y-m-d') : '-',
                'total_items' => (int) ($stockIn->total_items ?? 0),
                'total_value' => (float) ($stockIn->total_value ?? 0),
                'actions' => $actions,
            ];
        })->values()->all();

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
            'type' => 'required|in:PURCHASE,RETURN,ADJUSTMENT,PRODUCTION',
            'transaction_date' => 'required|date',
            'notes' => 'nullable',
        ]);

        $stockIn = $this->stockInService->create([
            ...$validated,
            'received_by' => auth()->id(),
        ]);

        return redirect()->route('stock-ins.show', $stockIn)->with('success', 'Stock In created successfully');
    }

    public function show(StockIn $stockIn)
    {
        $stockIn->load(['warehouse', 'supplier', 'items.product', 'receivedBy']);
        return view('stock-ins.show', compact('stockIn'));
    }

    public function pending(StockIn $stockIn)
    {
        try {
            $this->stockInService->markAsPending($stockIn);
            return redirect()->back()->with('success', 'Stock In marked as pending');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approve(StockIn $stockIn)
    {
        $this->authorize('update', $stockIn);

        try {
            $this->stockInService->approve($stockIn, auth()->user());
            return redirect()->back()->with('success', 'Stock In approved');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(StockIn $stockIn)
    {
        $this->authorize('update', $stockIn);

        try {
            $this->stockInService->reject($stockIn, auth()->user());
            return redirect()->back()->with('success', 'Stock In rejected');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(StockIn $stockIn)
    {
        if ($stockIn->status === 'DRAFT') {
            $stockIn->delete();
            return redirect()->route('stock-ins.index')->with('success', 'Stock In deleted');
        }

        return redirect()->back()->with('error', 'Cannot delete approved or pending stock in');
    }
}
