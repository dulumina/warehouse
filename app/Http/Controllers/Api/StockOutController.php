<?php

namespace App\Http\Controllers\Api;

use App\Models\StockOut;
use App\Http\Requests\StockTransaction\StoreStockOutRequest;
use App\Services\StockOutService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    use ApiResponse;

    public function __construct(protected StockOutService $stockOutService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('create stock out');

        $query = StockOut::with(['warehouse', 'issuedBy', 'items']);

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $stockOuts = $query->orderByDesc('created_at')->paginate(15);

        return $this->success($stockOuts, 'Stock outs retrieved successfully');
    }

    public function store(StoreStockOutRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $items = $validated['items'];
        unset($validated['items']);

        $validated['issued_by'] = $request->user()->id;

        $stockOut = $this->stockOutService->create($validated);

        foreach ($items as $itemData) {
            $this->stockOutService->addItem($stockOut, $itemData);
        }

        return $this->success(
            $stockOut->load(['warehouse', 'items.product', 'issuedBy']),
            'Stock out created successfully',
            201
        );
    }

    public function show(StockOut $stockOut): JsonResponse
    {
        $this->authorize('create stock out');

        return $this->success(
            $stockOut->load(['warehouse', 'issuedBy', 'items.product']),
            'Stock out retrieved successfully'
        );
    }

    public function approve(StockOut $stockOut, Request $request): JsonResponse
    {
        $this->authorize('approve stock out');

        try {
            $this->stockOutService->approve($stockOut, $request->user());

            return $this->success(
                $stockOut->load(['warehouse', 'items']),
                'Stock out approved successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function reject(StockOut $stockOut, Request $request): JsonResponse
    {
        $this->authorize('approve stock out');

        try {
            $this->stockOutService->reject($stockOut, $request->user());

            return $this->success($stockOut, 'Stock out rejected successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function markAsPending(StockOut $stockOut): JsonResponse
    {
        $this->authorize('create stock out');

        try {
            $this->stockOutService->markAsPending($stockOut);

            return $this->success($stockOut, 'Stock out marked as pending');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function destroy(StockOut $stockOut): JsonResponse
    {
        $this->authorize('create stock out');

        if (!$this->stockOutService->isDraft($stockOut)) {
            return $this->error('Only draft stock out can be deleted', 422);
        }

        $stockOut->delete();

        return $this->success(null, 'Stock out deleted successfully');
    }
}
