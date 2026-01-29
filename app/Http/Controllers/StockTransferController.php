<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Services\StockTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function __construct(protected StockTransferService $stockTransferService)
    {
    }

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
            $length = 10;
        }

        $search = $request->get('search')['value'] ?? '';
        $order = $request->get('order');

        $query = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'sentBy', 'receivedBy']);

        if ($request->has('status')) {
            $query->where('status', strtoupper($request->status));
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                    ->orWhereHas('fromWarehouse', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('toWarehouse', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $totalRecords = StockTransfer::count();
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

        $transfers = $query->skip($start)->take($length)->get();

        $data = $transfers->map(function ($transfer) {
            $viewUrl = route('stock-transfers.show', $transfer);
            
            $actions = "<div class='flex justify-center gap-2'>" .
                "<a href='{$viewUrl}' class='btn btn-sm btn-info' title='View'><i class='ti ti-eye'></i></a>";

            if (strtoupper($transfer->status) === 'DRAFT') {
                $deleteUrl = route('stock-transfers.destroy', $transfer);
                $sendUrl = route('stock-transfers.send', $transfer);
                $actions .= "<form action='{$sendUrl}' method='POST' class='inline' onsubmit='return confirm(\"Send this transfer?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<button type='submit' class='btn btn-sm btn-warning' title='Send'><i class='ti ti-send'></i></button>" .
                    "</form>";
                $actions .= "<form action='{$deleteUrl}' method='POST' class='inline' onsubmit='return confirm(\"Delete this transfer?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<input type='hidden' name='_method' value='DELETE'>" .
                    "<button type='submit' class='btn btn-sm btn-error' title='Delete'><i class='ti ti-trash'></i></button>" .
                    "</form>";
            }

            if (strtoupper($transfer->status) === 'IN_TRANSIT') {
                $receiveUrl = route('stock-transfers.receive', $transfer);
                $actions .= "<form action='{$receiveUrl}' method='POST' class='inline' onsubmit='return confirm(\"Receive this transfer?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<button type='submit' class='btn btn-sm btn-success' title='Receive'><i class='ti ti-package-import'></i></button>" .
                    "</form>";
            }
            
            $actions .= "</div>";

            $status = strtoupper($transfer->status);
            $badgeClass = match($status) {
                'RECEIVED'   => 'badge-success',
                'IN_TRANSIT' => 'badge-warning',
                'DRAFT'      => 'badge-ghost',
                'REJECTED'   => 'badge-error',
                'CANCELLED'  => 'badge-ghost',
                default      => 'badge-ghost'
            };

            return [
                'document_number' => "<span class='font-mono text-blue-600'>{$transfer->document_number}</span>",
                'source_warehouse' => $transfer->fromWarehouse?->name ?? '-',
                'destination_warehouse' => $transfer->toWarehouse?->name ?? '-',
                'status' => "<span class='badge {$badgeClass} gap-2'>{$status}</span>",
                'transaction_date' => $transfer->transaction_date ? $transfer->transaction_date->format('Y-m-d') : '-',
                'total_items' => (int) ($transfer->total_items ?? 0),
                'total_quantity' => (float) ($transfer->total_quantity ?? 0),
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
        $warehouses = Warehouse::with('locations')
            ->where('is_active', true)
            ->get();

        $products = Product::with('unit')
            ->where('is_active', true)
            ->get();

        $locations = $warehouses->flatMap(function ($w) {
            return $w->locations->map(function ($l) use ($w) {
                return [
                    'warehouse_id' => $w->id,
                    'id'           => $l->id,
                    'code'         => $l->code,
                    'name'         => $l->name,
                ];
            });
        })->values();

        return view('stock-transfers.create', compact(
            'warehouses',
            'products',
            'locations'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transaction_date' => 'required|date',
            'status' => 'required|in:DRAFT,IN_TRANSIT,RECEIVED,REJECTED,CANCELLED',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.from_location_id' => 'nullable|exists:warehouse_locations,id',
            'items.*.to_location_id' => 'nullable|exists:warehouse_locations,id',
            'items.*.batch_number' => 'nullable|string|max:255',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            $transfer = $this->stockTransferService->create([
                ...$validated,
                'sent_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $itemData) {
                $this->stockTransferService->addItem($transfer, $itemData);
            }

            return redirect()
                ->route('stock-transfers.show', $transfer)
                ->with('success', 'Stock Transfer created successfully');
                
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create transfer: ' . $e->getMessage());
        }
    }

    public function show(StockTransfer $transfer)
    {
        $transfer->load([
            'fromWarehouse', 
            'toWarehouse', 
            'items.product.unit',
            'sentBy',
            'receivedBy'
        ]);
        return view('stock-transfers.show', compact('transfer'));
    }

    public function send(StockTransfer $transfer)
    {
        try {
            $this->stockTransferService->send($transfer, auth()->user());
            return redirect()->back()->with('success', 'Stock Transfer sent successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function receive(StockTransfer $transfer)
    {
        try {
            // In a real app, we might allow partial receipt via a form.
            // For the datatables shortcut, we assume full receipt.
            $itemsReceived = $transfer->items->pluck('quantity', 'id')->toArray();
            $this->stockTransferService->receive($transfer, $itemsReceived, auth()->user());
            return redirect()->back()->with('success', 'Stock Transfer received successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(StockTransfer $transfer)
    {
        if (strtoupper($transfer->status) !== 'DRAFT') {
            return redirect()->back()->with('error', 'Cannot delete non-draft stock transfer');
        }

        $transfer->delete();
        return redirect()->route('stock-transfers.index')->with('success', 'Stock Transfer deleted successfully');
    }
}