<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Services\StockAdjustmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function __construct(protected StockAdjustmentService $stockAdjustmentService)
    {
    }

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
            $length = 10;
        }

        $search = $request->get('search')['value'] ?? '';
        $order = $request->get('order');

        $query = StockAdjustment::with(['warehouse', 'adjustedBy', 'items']);

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

        $totalRecords = StockAdjustment::count();
        $filteredRecords = $query->count();

        if ($order && is_array($order) && count($order) > 0) {
            $columnIndex = $order[0]['column'] ?? 0;
            $columns = $request->get('columns') ?? [];

            if (isset($columns[$columnIndex]['data'])) {
                $columnName = $columns[$columnIndex]['data'];
                $columnDir = strtoupper($order[0]['dir'] ?? 'ASC');

                if (in_array($columnName, ['document_number', 'status', 'adjustment_date'])) {
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

        $adjustments = $query->skip($start)->take($length)->get();

        $data = $adjustments->map(function ($adjustment) {
            $viewUrl = route('stock-adjustments.show', $adjustment);
            
            $actions = "<div class='flex justify-center gap-2'>" .
                "<a href='{$viewUrl}' class='btn btn-sm btn-info' title='View'><i class='ti ti-eye'></i></a>";

            if (strtoupper($adjustment->status) === 'DRAFT') {
                $approveUrl = route('stock-adjustments.approve', $adjustment);
                $rejectUrl = route('stock-adjustments.reject', $adjustment);
                $deleteUrl = route('stock-adjustments.destroy', $adjustment);
                
                $actions .= "<form action='{$approveUrl}' method='POST' class='inline' onsubmit='return confirm(\"Approve this adjustment?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<button type='submit' class='btn btn-sm btn-success' title='Approve'><i class='ti ti-check'></i></button>" .
                    "</form>";
                $actions .= "<form action='{$rejectUrl}' method='POST' class='inline' onsubmit='return confirm(\"Reject this adjustment?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<button type='submit' class='btn btn-sm btn-error' title='Reject'><i class='ti ti-x'></i></button>" .
                    "</form>";
                $actions .= "<form action='{$deleteUrl}' method='POST' class='inline' onsubmit='return confirm(\"Delete this adjustment?\")'>" .
                    "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                    "<input type='hidden' name='_method' value='DELETE'>" .
                    "<button type='submit' class='btn btn-sm btn-error' title='Delete'><i class='ti ti-trash'></i></button>" .
                    "</form>";
            }
            
            $actions .= "</div>";

            $status = strtoupper($adjustment->status);
            $badgeClass = match($status) {
                'APPROVED' => 'badge-success',
                'REJECTED' => 'badge-error',
                'DRAFT'    => 'badge-ghost',
                default    => 'badge-ghost'
            };

            $typeLabels = [
                'PHYSICAL_COUNT' => 'Physical Count',
                'CORRECTION' => 'Correction',
                'DAMAGED' => 'Damaged',
                'EXPIRED' => 'Expired',
                'FOUND' => 'Found',
            ];

            return [
                'document_number' => "<span class='font-mono text-blue-600'>{$adjustment->document_number}</span>",
                'warehouse' => $adjustment->warehouse?->name ?? '-',
                'type' => $typeLabels[$adjustment->type] ?? $adjustment->type,
                'status' => "<span class='badge {$badgeClass} gap-2'>{$status}</span>",
                'adjustment_date' => $adjustment->adjustment_date ? $adjustment->adjustment_date->format('Y-m-d') : '-',
                'total_items' => (int) $adjustment->items->count(),
                'value_impact' => (float) $adjustment->items->sum('value_difference'),
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
        $warehouses = Warehouse::with('locations')->where('is_active', true)->get();
        $products = Product::with('unit')->where('is_active', true)->get();
        $warehouseLocations = $warehouses->flatMap(function ($w) {
            return $w->locations->map(function ($l) use ($w) {
                return ['warehouse_id' => $w->id, 'id' => $l->id, 'code' => $l->code, 'name' => $l->name];
            });
        })->values();

        return view('stock-adjustments.create', compact('warehouses', 'products', 'warehouseLocations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'adjustment_date' => 'required|date',
            'type' => 'required|in:PHYSICAL_COUNT,CORRECTION,DAMAGED,EXPIRED,FOUND',
            'status' => 'required|in:DRAFT,APPROVED,REJECTED',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.location_id' => 'nullable|exists:warehouse_locations,id',
            'items.*.batch_number' => 'nullable|string|max:255',
            'items.*.actual_quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $adjustment = $this->stockAdjustmentService->create([
                'adjustment_date' => $validated['adjustment_date'],
                'warehouse_id' => $validated['warehouse_id'],
                'type' => $validated['type'],
                'status' => 'DRAFT',
                'notes' => $validated['notes'] ?? null,
                'adjusted_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $itemData) {
                $this->stockAdjustmentService->addItem($adjustment, $itemData);
            }

            if (strtoupper($validated['status']) === 'APPROVED') {
                $this->stockAdjustmentService->approve($adjustment, auth()->user());
            }

            DB::commit();
            return redirect()->route('stock-adjustments.show', $adjustment)->with('success', 'Stock Adjustment created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to create adjustment: ' . $e->getMessage());
        }
    }

    public function show(StockAdjustment $adjustment)
    {
        $adjustment->load(['warehouse', 'items.product.unit', 'adjustedBy', 'approvedBy']);
        return view('stock-adjustments.show', compact('adjustment'));
    }

    public function approve(StockAdjustment $adjustment)
    {
        try {
            $this->stockAdjustmentService->approve($adjustment, auth()->user());
            return redirect()->back()->with('success', 'Stock Adjustment approved successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(StockAdjustment $adjustment)
    {
        try {
            $this->stockAdjustmentService->reject($adjustment, auth()->user());
            return redirect()->back()->with('success', 'Stock Adjustment rejected');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(StockAdjustment $adjustment)
    {
        if (strtoupper($adjustment->status) !== 'DRAFT') {
            return redirect()->back()->with('error', 'Cannot delete non-draft stock adjustment');
        }

        $adjustment->delete();
        return redirect()->route('stock-adjustments.index')->with('success', 'Stock Adjustment deleted successfully');
    }
}