<?php

namespace App\Http\Controllers\Api;

use App\Models\Warehouse;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $this->authorize('view warehouses');

        $warehouses = Warehouse::with('manager')
            ->paginate(15);

        return $this->success($warehouses, 'Warehouses retrieved successfully');
    }

    public function store(StoreWarehouseRequest $request): JsonResponse
    {
        $warehouse = Warehouse::create($request->validated());

        return $this->success($warehouse->load('manager'), 'Warehouse created successfully', 201);
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        $this->authorize('view warehouses');

        return $this->success($warehouse->load(['manager', 'locations']), 'Warehouse retrieved successfully');
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): JsonResponse
    {
        $warehouse->update($request->validated());

        return $this->success($warehouse->load('manager'), 'Warehouse updated successfully');
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $this->authorize('delete warehouse');

        $warehouse->delete();

        return $this->success(null, 'Warehouse deleted successfully');
    }

    public function getLocations(Warehouse $warehouse): JsonResponse
    {
        $this->authorize('manage warehouse locations');

        $locations = $warehouse->locations()
            ->where('is_active', true)
            ->get();

        return $this->success($locations, 'Warehouse locations retrieved successfully');
    }
}
