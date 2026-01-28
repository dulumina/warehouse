<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    public function index()
    {
        return view('stock-transfers.index');
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

        $query = StockTransfer::with(['sourceWarehouse', 'destinationWarehouse', 'sentBy', 'receivedBy']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                    ->orWhereHas('sourceWarehouse', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('destinationWarehouse', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $totalRecords = StockTransfer::count();

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
        $transfers = $query->get();

        $data = $transfers->map(function ($transfer) {
            $viewUrl = route('stock-transfers.show', $transfer);
            
            $actions = "<div class='flex justify-center gap-2'>" .
                "<a href='{$viewUrl}' class='btn btn-sm btn-info' title='View'><i class='ti ti-eye'></i></a>";

            if ($transfer->status === 'draft') {
                $deleteUrl = route('stock-transfers.destroy', $transfer);
                $actions .= "<form action='{$deleteUrl}' method='POST' class='inline' onsubmit='return confirm(\"Delete this transfer?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<input type='hidden' name='_method' value='DELETE'>" .
                    "<button type='submit' class='btn btn-sm btn-error' title='Delete'><i class='ti ti-trash'></i></button>" .
                    "</form>";
            }
            
            $actions .= "</div>";

            $badgeClass = match($transfer->status) {
                'received' => 'badge-success',
                'in_transit' => 'badge-warning',
                default => 'badge-ghost'
            };

            return [
                'document_number' => "<span class='font-mono text-blue-600'>{$transfer->document_number}</span>",
                'source_warehouse' => $transfer->sourceWarehouse?->name ?? '-',
                'destination_warehouse' => $transfer->destinationWarehouse?->name ?? '-',
                'status' => "<span class='badge {$badgeClass} gap-2'>{$transfer->status}</span>",
                'transaction_date' => $transfer->transaction_date?->format('Y-m-d'),
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
