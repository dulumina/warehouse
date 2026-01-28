<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'unit'])->paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $units = \App\Models\Unit::all();
        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products',
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable',
            'type' => 'required|in:raw,finished,service',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'required|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
        ]);

        Product::create($validated);
        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'suppliers', 'inventory']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $units = \App\Models\Unit::all();
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products,code,' . $product->id,
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable',
            'type' => 'required|in:raw,finished,service',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'required|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
        ]);

        $product->update($validated);
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
