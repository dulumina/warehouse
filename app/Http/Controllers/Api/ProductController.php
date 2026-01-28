<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Requests\Product\StoreProductRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('view products');

        $query = Product::with(['category', 'unit', 'suppliers']);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter active only
        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        $products = $query->paginate(15);

        return $this->success($products, 'Products retrieved successfully');
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return $this->success($product->load(['category', 'unit']), 'Product created successfully', 201);
    }

    public function show(Product $product): JsonResponse
    {
        $this->authorize('view products');

        return $this->success(
            $product->load(['category', 'unit', 'suppliers', 'inventory']),
            'Product retrieved successfully'
        );
    }

    public function update(StoreProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize('edit product');

        $product->update($request->validated());

        return $this->success($product->load(['category', 'unit']), 'Product updated successfully');
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete product');

        $product->delete();

        return $this->success(null, 'Product deleted successfully');
    }

    public function inventory(Product $product): JsonResponse
    {
        $this->authorize('view inventory');

        $inventory = $product->inventory()
            ->with('warehouse')
            ->get();

        return $this->success($inventory, 'Product inventory retrieved successfully');
    }
}
