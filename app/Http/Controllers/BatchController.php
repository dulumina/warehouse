<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        return view('batches.index');
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

        $query = Batch::with(['product', 'supplier', 'inventory']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $totalRecords = Batch::count();

        if ($order && is_array($order) && count($order) > 0) {
            $columnIndex = $order[0]['column'] ?? 0;
            $columns = $request->get('columns') ?? [];

            if (isset($columns[$columnIndex]['data'])) {
                $columnName = $columns[$columnIndex]['data'];
                $columnDir = strtoupper($order[0]['dir'] ?? 'ASC');

                if (in_array($columnName, ['batch_number', 'expiry_date', 'quantity', 'status'])) {
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
        $batches = $query->get();

        $data = $batches->map(function ($batch) {
            $viewUrl = route('batches.show', $batch);
            
            $actions = "<div class='flex justify-center gap-2'>" .
                "<a href='{$viewUrl}' class='btn btn-sm btn-info' title='View'><i class='ti ti-eye'></i></a></div>";

            return [
                'batch_number' => "<span class='font-mono text-blue-600'>{$batch->batch_number}</span>",
                'product' => $batch->product?->name ?? '-',
                'expiry_date' => $batch->expiry_date?->format('Y-m-d') ?? '-',
                'quantity' => $batch->quantity,
                'status' => "<span class='badge'>{$batch->status}</span>",
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

    public function show(Batch $batch)
    {
        $batch->load(['product', 'inventory', 'serialNumbers']);
        return view('batches.show', compact('batch'));
    }
}