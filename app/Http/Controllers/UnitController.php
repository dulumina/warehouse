<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('units.index');
    }

    /**
     * Datatables server-side
     */
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

        $query = Unit::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('symbol', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $totalRecords = Unit::count();

        if ($order && is_array($order) && count($order) > 0) {
            $columnIndex = $order[0]['column'] ?? 0;
            $columns = $request->get('columns') ?? [];

            if (isset($columns[$columnIndex]['data'])) {
                $columnName = $columns[$columnIndex]['data'];
                $columnDir = strtoupper($order[0]['dir'] ?? 'ASC');

                if (in_array($columnName, ['code', 'name', 'symbol', 'description'])) {
                    $query->orderBy($columnName, $columnDir);
                } else {
                    $query->orderBy('code', 'asc');
                }
            } else {
                $query->orderBy('code', 'asc');
            }
        } else {
            $query->orderBy('code', 'asc');
        }

        $filteredRecords = $query->count();

        $query->limit($length);
        if ($start > 0) {
            $query->offset($start);
        }
        $units = $query->get();

        $data = $units->map(function ($unit) {
            $editUrl = route('units.edit', $unit);
            $deleteUrl = route('units.destroy', $unit);

            $actions = "<div class='flex justify-center gap-2'>" .
                "<a href='{$editUrl}' class='btn btn-sm btn-warning' title='Edit'><i class='ti ti-edit'></i></a>" .
                "<form action='{$deleteUrl}' method='POST' class='inline' onsubmit='return confirm(\"Delete this unit?\")'>" .
                "<input type='hidden' name='_token' value='" . csrf_token() . "'>" .
                "<input type='hidden' name='_method' value='DELETE'>" .
                "<button type='submit' class='btn btn-sm btn-error' title='Delete'><i class='ti ti-trash'></i></button>" .
                "</form></div>";

            return [
                'code' => $unit->code,
                'name' => $unit->name,
                'symbol' => $unit->symbol,
                'description' => $unit->description,
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:units,code',
            'name'        => 'required|string|max:100',
            'symbol'      => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        Unit::create($validated);

        return redirect()
            ->route('units.index')
            ->with('success', 'Unit berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $unit = Unit::findOrFail($id);
        return view('units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $unit = Unit::findOrFail($id);

        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:units,code,' . $unit->id,
            'name'        => 'required|string|max:100',
            'symbol'      => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $unit->update($validated);

        return redirect()
            ->route('units.index')
            ->with('success', 'Unit berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit deleted successfully');
    }
}
