<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Services\StockOutService;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    public function __construct(protected StockOutService $stockOutService)
    {
    }

    public function index()
    {
        return view('stock-outs.index');
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

        $query = StockOut::with(['warehouse']);

        if ($request->has('status')) {
            $query->where('status', strtoupper($request->status));
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                    ->orWhereHas('warehouse', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $totalRecords = StockOut::count();
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

        $stockOuts = $query->skip($start)->take($length)->get();

        $data = $stockOuts->map(function ($stockOut) {
            $viewUrl = route('stock-outs.show', $stockOut);
            
            $actions = "<div class='flex justify-center gap-2'>" .
                "<a href='{$viewUrl}' class='btn btn-sm btn-info' title='View'><i class='ti ti-eye'></i></a>";

            if (strtoupper($stockOut->status) === 'DRAFT') {
                $deleteUrl = route('stock-outs.destroy', $stockOut);
                $actions .= "<form action='{$deleteUrl}' method='POST' class='inline' onsubmit='return confirm(\"Delete this stock out?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<input type='hidden' name='_method' value='DELETE'>" .
                    "<button type='submit' class='btn btn-sm btn-error' title='Delete'><i class='ti ti-trash'></i></button>" .
                    "</form>";
            }

            if (strtoupper($stockOut->status) === 'PENDING') {
                $approveUrl = route('stock-outs.approve', $stockOut);
                $rejectUrl = route('stock-outs.reject', $stockOut);
                $actions .= "<form action='{$approveUrl}' method='POST' class='inline' onsubmit='return confirm(\"Approve this stock out?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<button type='submit' class='btn btn-sm btn-success' title='Approve'><i class='ti ti-check'></i></button>" .
                    "</form>";
                $actions .= "<form action='{$rejectUrl}' method='POST' class='inline' onsubmit='return confirm(\"Reject this stock out?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<button type='submit' class='btn btn-sm btn-error' title='Reject'><i class='ti ti-x'></i></button>" .
                    "</form>";
            }
            
            $actions .= "</div>";

            $status = strtoupper($stockOut->status);
            $badgeClass = match($status) {
                'APPROVED' => 'badge-success',
                'REJECTED' => 'badge-error',
                'PENDING'  => 'badge-warning',
                'DRAFT'    => 'badge-ghost',
                default    => 'badge-ghost'
            };

            return [
                'document_number' => "<span class='font-mono text-blue-600'>{$stockOut->document_number}</span>",
                'warehouse' => $stockOut->warehouse?->name ?? '-',
                'type' => ucfirst(strtolower($stockOut->type ?? 'SALES')),
                'customer_name' => $stockOut->customer_name ?? '-',
                'status' => "<span class='badge {$badgeClass} gap-2'>{$status}</span>",
                'transaction_date' => $stockOut->transaction_date ? $stockOut->transaction_date->format('Y-m-d') : '-',
                'total_items' => (int) ($stockOut->total_items ?? 0),
                'total_quantity' => (float) ($stockOut->total_quantity ?? 0),
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
        $products = \App\Models\Product::all();
        return view('stock-outs.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:SALES,RETURN,ADJUSTMENT,PRODUCTION,DAMAGED',
            'transaction_date' => 'required|date',
            'notes' => 'nullable',
        ]);

        $stockOut = $this->stockOutService->create([
            ...$validated,
            'issued_by' => auth()->id(),
        ]);

        return redirect()->route('stock-outs.show', $stockOut)->with('success', 'Stock Out created successfully');
    }

    public function show(StockOut $stockOut)
    {
        $stockOut->load(['warehouse', 'items.product', 'issuedBy']);
        return view('stock-outs.show', compact('stockOut'));
    }

    public function pending(StockOut $stockOut)
    {
        try {
            $this->stockOutService->markAsPending($stockOut);
            return redirect()->back()->with('success', 'Stock Out marked as pending');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approve(StockOut $stockOut)
    {
        $this->authorize('update', $stockOut);

        try {
            $this->stockOutService->approve($stockOut, auth()->user());
            return redirect()->back()->with('success', 'Stock Out approved');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(StockOut $stockOut)
    {
        $this->authorize('update', $stockOut);

        try {
            $this->stockOutService->reject($stockOut, auth()->user());
            return redirect()->back()->with('success', 'Stock Out rejected');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(StockOut $stockOut)
    {
        if (strtoupper($stockOut->status) === 'DRAFT') {
            $stockOut->delete();
            return redirect()->route('stock-outs.index')->with('success', 'Stock Out deleted');
        }

        return redirect()->back()->with('error', 'Cannot delete approved or pending stock out');
    }
}
