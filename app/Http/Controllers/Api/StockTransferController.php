<?php

namespace App\Http\Controllers\Api;

use App\Models\StockTransfer;
use App\Http\Requests\StockTransaction\StoreStockTransferRequest;
use App\Services\StockTransferService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    use ApiResponse;

    public function __construct(protected StockTransferService $transferService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('create stock transfer');

        $query = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'sentBy', 'items']);

        if ($request->has('from_warehouse_id')) {
            $query->where('from_warehouse_id', $request->from_warehouse_id);
        }

        if ($request->has('to_warehouse_id')) {
            $query->where('to_warehouse_id', $request->to_warehouse_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $transfers = $query->orderByDesc('created_at')->paginate(15);

        return $this->success($transfers, 'Stock transfers retrieved successfully');
    }

    public function store(StoreStockTransferRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $items = $validated['items'];
        unset($validated['items']);

        $validated['sent_by'] = $request->user()->id;

        $transfer = $this->transferService->create($validated);

        foreach ($items as $itemData) {
            $this->transferService->addItem($transfer, $itemData);
        }

        return $this->success(
            $transfer->load(['fromWarehouse', 'toWarehouse', 'items.product']),
            'Stock transfer created successfully',
            201
        );
    }

    public function show(StockTransfer $transfer): JsonResponse
    {
        $this->authorize('create stock transfer');

        return $this->success(
            $transfer->load(['fromWarehouse', 'toWarehouse', 'sentBy', 'items.product']),
            'Stock transfer retrieved successfully'
        );
    }

    public function send(StockTransfer $transfer, Request $request): JsonResponse
    {
        $this->authorize('create stock transfer');

        try {
            $this->transferService->send($transfer, $request->user());

            return $this->success(
                $transfer->load(['fromWarehouse', 'toWarehouse', 'items']),
                'Stock transfer sent successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function receive(StockTransfer $transfer, Request $request): JsonResponse
    {
        $this->authorize('create stock transfer');

        try {
            $request->validate([
                'items_received' => ['required', 'array'],
            ]);

            $this->transferService->receive($transfer, $request->items_received, $request->user());

            return $this->success(
                $transfer->load(['fromWarehouse', 'toWarehouse', 'items']),
                'Stock transfer received successfully'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function destroy(StockTransfer $transfer): JsonResponse
    {
        $this->authorize('create stock transfer');

        if (!$this->transferService->isDraft($transfer)) {
            return $this->error('Only draft transfers can be deleted', 422);
        }

        $transfer->delete();

        return $this->success(null, 'Stock transfer deleted successfully');
    }
}
