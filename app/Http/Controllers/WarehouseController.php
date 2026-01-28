<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('warehouses.index');
    }

    public function datatables(Request $request)
    {
        $draw = (int) $request->get('draw', 0);
        $start = max(0, (int) $request->get('start', 0));
        $length = (int) $request->get('length', 10);

        // Handle invalid or -1 length (show all records)
        if ($length < 1) {
            $length = PHP_INT_MAX; // Show all records without pagination limit
        }

        $search = $request->get('search')['value'] ?? '';
        $order = $request->get('order');

        // Base query
        $query = Warehouse::query();

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Total records before filtering
        $totalRecords = Warehouse::count();

        // Order (apply before counting filtered records)
        if ($order && is_array($order) && count($order) > 0) {
            $columnIndex = $order[0]['column'] ?? 0;
            $columns = $request->get('columns') ?? [];

            if (isset($columns[$columnIndex]['data'])) {
                $columnName = $columns[$columnIndex]['data'];
                $columnDir = strtoupper($order[0]['dir'] ?? 'ASC');

                // Only order by valid columns
                if (in_array($columnName, ['code', 'name', 'city', 'province', 'phone', 'email', 'created_at'])) {
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

        // Filtered records count (AFTER search and order, BEFORE limit/offset)
        $filteredRecords = $query->count();

        // Apply limit and offset for pagination
        $query->limit($length);
        if ($start > 0) {
            $query->offset($start);
        }
        $warehouses = $query->get();

        // Format data
        $data = $warehouses->map(function ($warehouse) {
            $viewUrl = route('warehouses.show', $warehouse);
            $editUrl = route('warehouses.edit', $warehouse);
            $deleteUrl = route('warehouses.destroy', $warehouse);

            $actions = "<div class='flex justify-center gap-2'>" .
                "<a href='{$viewUrl}' class='btn btn-sm btn-info' title='View'><i class='ti ti-eye'></i></a>" .
                "<a href='{$editUrl}' class='btn btn-sm btn-warning' title='Edit'><i class='ti ti-edit'></i></a>" .
                "<form action='{$deleteUrl}' method='POST' class='inline' onsubmit='return confirm(\"Delete this warehouse?\")'>" .
                "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                "<input type='hidden' name='_method' value='DELETE'>" .
                "<button type='submit' class='btn btn-sm btn-error' title='Delete'><i class='ti ti-trash'></i></button>" .
                "</form></div>";

            return [
                'code' => $warehouse->code,
                'name' => $warehouse->name,
                'city' => $warehouse->city,
                'phone' => $warehouse->phone ?? '-',
                'email' => $warehouse->email ?? '-',
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
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:warehouses',
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'province' => 'required',
            'postal_code' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
        ]);

        Warehouse::create($validated);
        return redirect()->route('warehouses.index')->with('success', 'Warehouse created successfully');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load('locations', 'inventory');
        return view('warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'code' => 'required|unique:warehouses,code,' . $warehouse->id,
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'province' => 'required',
            'postal_code' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
        ]);

        $warehouse->update($validated);
        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully');
    }
}
