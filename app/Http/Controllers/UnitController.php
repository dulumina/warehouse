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
        $columns = [
            0 => 'code',
            1 => 'name',
            2 => 'symbol',
            3 => 'description',
        ];

        $query = Unit::query();

        // Search
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('symbol', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $recordsTotal = Unit::count();
        $recordsFiltered = $query->count();

        // Order
        if ($request->has('order')) {
            $orderColumn = $columns[$request->order[0]['column']];
            $orderDir = $request->order[0]['dir'];
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->orderBy('code', 'asc');
        }

        // Pagination (🔥 FIX MARIA DB)
        $start  = intval($request->start ?? 0);
        $length = intval($request->length ?? 10);

        if ($length > 0) {
            $query->limit($length)->offset($start);
        }

        $units = $query->get();

        $data = [];
        foreach ($units as $unit) {
            $data[] = [
                'code' => $unit->code,
                'name' => $unit->name,
                'symbol' => $unit->symbol,
                'description' => $unit->description,
                'actions' => '
                    <div class="flex justify-end gap-2">
                        <a href="'.route('units.edit', $unit->id).'" class="btn btn-sm btn-warning">
                            <i class="ti ti-edit"></i>
                        </a>
                        <button onclick="deleteUnit(\''.route('units.destroy', $unit->id).'\')"
                            class="btn btn-sm btn-error">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                ',
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
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
    public function destroy(string $id)
    {
        Unit::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit berhasil dihapus'
        ]);
    }
}
