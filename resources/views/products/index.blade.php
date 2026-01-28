@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Products</h1>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="ti ti-plus mr-2"></i> Add Product
            </a>
        </div>

        @if ($products->isEmpty())
            <div class="bg-gray-50 rounded-lg p-8 text-center">
                <i class="ti ti-package text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No products found</p>
            </div>
        @else
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold">Code</th>
                            <th class="px-6 py-3 text-left font-semibold">Name</th>
                            <th class="px-6 py-3 text-left font-semibold">Category</th>
                            <th class="px-6 py-3 text-left font-semibold">Cost</th>
                            <th class="px-6 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-mono text-blue-600">{{ $product->code }}</td>
                                <td class="px-6 py-4 font-medium">{{ $product->name }}</td>
                                <td class="px-6 py-4">{{ $product->category?->name ?? '-' }}</td>
                                <td class="px-6 py-4">${{ number_format($product->cost, 2) }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">View</a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
