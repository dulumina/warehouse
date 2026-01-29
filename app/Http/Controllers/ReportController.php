<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }
    public function stock(Request $request)
    {
        if ($request->ajax() && !$request->has('summary')) {
            return $this->stockDatatables($request);
        }

        $summary = [
            'total_items' => Inventory::sum('quantity'),
            'low_stock' => Inventory::join('products', 'inventory.product_id', '=', 'products.id')
                ->whereRaw('inventory.quantity <= products.reorder_point')
                ->count(),
            'total_value' => Inventory::get()->sum(function($i) {
                return (float)$i->quantity * (float)($i->product->standard_cost ?? 0);
            }),
        ];

        if ($request->has('summary')) {
            return response()->json(['summary' => $summary]);
        }

        return view('reports.stock', compact('summary'));
    }

    public function stockDatatables(Request $request)
    {
        $query = Inventory::with(['product', 'warehouse', 'location']);

        if ($search = $request->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
                })->orWhereHas('warehouse', function($wq) use ($search) {
                    $wq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $totalData = $query->count();
        $limit = intval($request->input('length', 10));
        $start = intval($request->input('start', 0));
        
        if ($limit !== -1) {
            $query->limit($limit)->offset($start);
        } elseif ($start > 0) {
            $query->limit(18446744073709551615)->offset($start);
        }
        
        $items = $query->get();

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalData),
            "data" => $items->map(function($item) {
                return [
                    'product' => $item->product->name . ' (' . $item->product->code . ')',
                    'warehouse' => $item->warehouse->name,
                    'location' => $item->location->name ?? '-',
                    'quantity' => number_format((float)$item->quantity, 2, ',', '.'),
                    'reorder' => number_format((float)($item->product->reorder_point ?? 0), 2, ',', '.'),
                    'status' => (float)$item->quantity <= (float)($item->product->reorder_point ?? 0) 
                        ? '<span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Low Stock</span>' 
                        : '<span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">In Stock</span>',
                ];
            })
        ]);
    }

    public function movements(Request $request)
    {
        if ($request->ajax() && !$request->has('summary')) {
            return $this->movementsDatatables($request);
        }

        $summary = [
            'total_movements' => StockMovement::count(),
            'in_count' => StockMovement::where('transaction_type', 'STOCK_IN')->count(),
            'out_count' => StockMovement::where('transaction_type', 'STOCK_OUT')->count(),
        ];

        if ($request->has('summary')) {
            return response()->json(['summary' => $summary]);
        }

        return view('reports.movements', compact('summary'));
    }

    public function movementsDatatables(Request $request)
    {
        $query = StockMovement::with(['product', 'warehouse', 'user', 'inventory']);

        if ($search = $request->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                })->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('transaction_type', $request->type);
        }

        $totalData = $query->count();
        $limit = intval($request->input('length', 10));
        $start = intval($request->input('start', 0));
        
        if ($limit !== -1) {
            $query->limit($limit)->offset($start);
        } elseif ($start > 0) {
            $query->limit(18446744073709551615)->offset($start);
        }
        
        $items = $query->latest()->get();

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalData),
            "data" => $items->map(function($item) {
                $type = $item->transaction_type;
                $typeClass = $type == 'STOCK_IN' || $type == 'TRANSFER_IN' ? 'text-green-600 bg-green-100' : ($type == 'STOCK_OUT' || $type == 'TRANSFER_OUT' ? 'text-red-600 bg-red-100' : 'text-blue-600 bg-blue-100');
                return [
                    'date' => $item->created_at->format('Y-m-d H:i'),
                    'product' => $item->product->name,
                    'warehouse' => $item->warehouse->name,
                    'type' => '<span class="px-2 py-1 text-xs font-semibold rounded-full '.$typeClass.'">'.$type.'</span>',
                    'quantity' => number_format((float)$item->quantity, 2, ',', '.'),
                    'doc' => $item->reference_number ?? '-',
                    'user' => $item->user->name ?? '-',
                ];
            })
        ]);
    }

    public function valuation(Request $request)
    {
        if ($request->ajax() && !$request->has('summary')) {
            return $this->valuationDatatables($request);
        }

        $total_value = Inventory::get()->sum(function($i) {
            return (float)$i->quantity * (float)($i->product->standard_cost ?? 0);
        });

        $summary = [
            'total_value' => $total_value,
            'avg_cost' => Inventory::count() > 0 ? $total_value / Inventory::count() : 0,
        ];

        if ($request->has('summary')) {
            return response()->json(['summary' => $summary]);
        }

        return view('reports.valuation', compact('summary'));
    }

    public function valuationDatatables(Request $request)
    {
        $query = Inventory::with(['product', 'warehouse']);

        if ($search = $request->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $totalData = $query->count();
        $limit = intval($request->input('length', 10));
        $start = intval($request->input('start', 0));
        
        if ($limit !== -1) {
            $query->limit($limit)->offset($start);
        } elseif ($start > 0) {
            $query->limit(18446744073709551615)->offset($start);
        }
        
        $items = $query->get();

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalData),
            "data" => $items->map(function($item) {
                $cost = (float)($item->product->standard_cost ?? 0);
                $total = (float)$item->quantity * $cost;
                return [
                    'product' => $item->product->name,
                    'warehouse' => $item->warehouse->name,
                    'quantity' => number_format((float)$item->quantity, 2, ',', '.'),
                    'unit_cost' => 'Rp ' . number_format($cost, 2, ',', '.'),
                    'total_value' => 'Rp ' . number_format($total, 2, ',', '.'),
                ];
            })
        ]);
    }
}