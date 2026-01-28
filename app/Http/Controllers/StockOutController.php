<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
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
            $length = PHP_INT_MAX;
        }

        $search = $request->get('search')['value'] ?? '';
        $order = $request->get('order');

        $query = StockOut::with(['warehouse']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                    ->orWhereHas('warehouse', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $totalRecords = StockOut::count();

        if ($order && is_array($order) && count($order) > 0) {
            $columnIndex = $order[0]['column'] ?? 0;
            $columns = $request->get('columns') ?? [];

            if (isset($columns[$columnIndex]['data'])) {
                $columnName = $columns[$columnIndex]['data'];
                $columnDir = strtoupper($order[0]['dir'] ?? 'ASC');

                if (in_array($columnName, ['document_number', 'type', 'status', 'transaction_date'])) {
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
        $stockOuts = $query->get();

        $data = $stockOuts->map(function ($stockOut) {
            $viewUrl = route('stock-outs.show', $stockOut);
            
            $actions = "<div class='flex justify-center gap-2'>" .
                "<a href='{$viewUrl}' class='btn btn-sm btn-info' title='View'><i class='ti ti-eye'></i></a>";

            if ($stockOut->status === 'draft') {
                $deleteUrl = route('stock-outs.destroy', $stockOut);
                $actions .= "<form action='{$deleteUrl}' method='POST' class='inline' onsubmit='return confirm(\"Delete this stock out?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<input type='hidden' name='_method' value='DELETE'>" .
                    "<button type='submit' class='btn btn-sm btn-error' title='Delete'><i class='ti ti-trash'></i></button>" .
                    "</form>";
            }
            
            $actions .= "</div>";

            $badgeClass = match($stockOut->status) {
                'approved' => 'badge-success',
                'rejected' => 'badge-error',
                'pending' => 'badge-warning',
                default => 'badge-ghost'
            };

            return [
                'document_number' => "<span class='font-mono text-blue-600'>{$stockOut->document_number}</span>",
                'warehouse' => $stockOut->warehouse?->name ?? '-',
                'type' => ucfirst($stockOut->type),
                'status' => "<span class='badge {$badgeClass} gap-2'>{$stockOut->status}</span>",
                'transaction_date' => $stockOut->transaction_date?->format('Y-m-d'),
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
