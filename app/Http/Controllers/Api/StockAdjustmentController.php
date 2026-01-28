<?php

namespace App\Http\Controllers\Api;

use App\Models\StockAdjustment;
use App\Http\Requests\StockTransaction\StoreStockAdjustmentRequest;
use App\Services\StockAdjustmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    use ApiResponse;

    public function __construct(protected StockAdjustmentService $adjustmentService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('adjust inventory');

        $query = StockAdjustment::with(['warehouse', 'adjustedBy', 'items']);

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $adjustments = $query->orderByDesc('created_at')->paginate(15);

        return $this->success($adjustments, 'Stock adjustments retrieved successfully');
    }

    public function store(StoreStockAdjustmentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $items = $validated['items'];
        unset($validated['items']);

        $validated['adjusted_by'] = $request->user()->id;

        $adjustment = $this->adjustmentService->create($validated);

        foreach ($items as $itemData) {
            $this->adjustmentService->addItem($adjustment, $itemData);
        }

        return $this->success(
            $adjustment->load(['warehouse', 'items.product']),
            'Stock adjustment created successfully',
            201
        );
    }

    public function show(StockAdjustment $adjustment): JsonResponse
    {
        $this->authorize('adjust inventory');

        return $this->success(
            $adjustment->load(['warehouse', 'adjustedBy', 'items.product']),
            'Stock adjustment retrieved successfully'
        );
    }

    public function approve(StockAdjustment $adjustment, Request $request): JsonResponse
    {
        $this->authorize('adjust inventory');

        try {
            $this->adjustmentService->approve($adjustment, $request->user());

            return $this->success(
                $adjustment->load(['warehouse', 'items']),
                'Stock adjustment approved successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function reject(StockAdjustment $adjustment, Request $request): JsonResponse
    {
        $this->authorize('adjust inventory');

        try {
            $this->adjustmentService->reject($adjustment, $request->user());

            return $this->success($adjustment, 'Stock adjustment rejected successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function destroy(StockAdjustment $adjustment): JsonResponse
    {
        $this->authorize('adjust inventory');

        if (!$this->adjustmentService->isDraft($adjustment)) {
            return $this->error('Only draft adjustments can be deleted', 422);
        }

        $adjustment->delete();

        return $this->success(null, 'Stock adjustment deleted successfully');
    }
}
