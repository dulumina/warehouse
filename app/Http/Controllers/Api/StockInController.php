<?php

namespace App\Http\Controllers\Api;

use App\Models\StockIn;
use App\Http\Requests\StockTransaction\StoreStockInRequest;
use App\Services\StockInService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockInController extends Controller
{
    use ApiResponse;

    public function __construct(protected StockInService $stockInService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('create stock in');

        $query = StockIn::with(['warehouse', 'supplier', 'receivedBy', 'items']);

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $stockIns = $query->orderByDesc('created_at')->paginate(15);

        return $this->success($stockIns, 'Stock ins retrieved successfully');
    }

    public function store(StoreStockInRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $items = $validated['items'];
        unset($validated['items']);

        $validated['received_by'] = $request->user()->id;

        $stockIn = $this->stockInService->create($validated);

        foreach ($items as $itemData) {
            $this->stockInService->addItem($stockIn, $itemData);
        }

        return $this->success(
            $stockIn->load(['warehouse', 'items.product', 'receivedBy']),
            'Stock in created successfully',
            201
        );
    }

    public function show(StockIn $stockIn): JsonResponse
    {
        $this->authorize('create stock in');

        return $this->success(
            $stockIn->load(['warehouse', 'supplier', 'receivedBy', 'items.product']),
            'Stock in retrieved successfully'
        );
    }

    public function approve(StockIn $stockIn, Request $request): JsonResponse
    {
        $this->authorize('approve stock in');

        try {
            $this->stockInService->approve($stockIn, $request->user());

            return $this->success(
                $stockIn->load(['warehouse', 'items']),
                'Stock in approved successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function reject(StockIn $stockIn, Request $request): JsonResponse
    {
        $this->authorize('approve stock in');

        try {
            $this->stockInService->reject($stockIn, $request->user());

            return $this->success(
                $stockIn,
                'Stock in rejected successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function markAsPending(StockIn $stockIn): JsonResponse
    {
        $this->authorize('create stock in');

        try {
            $this->stockInService->markAsPending($stockIn);

            return $this->success($stockIn, 'Stock in marked as pending');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function destroy(StockIn $stockIn): JsonResponse
    {
        $this->authorize('create stock in');

        if (!$this->stockInService->isDraft($stockIn)) {
            return $this->error('Only draft stock in can be deleted', 422);
        }

        $stockIn->delete();

        return $this->success(null, 'Stock in deleted successfully');
    }
}
