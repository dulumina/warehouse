@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">← Back</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Code</p>
                    <p class="text-lg font-mono font-semibold">{{ $product->code }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm mb-1">Category</p>
                    <p class="text-lg font-semibold">{{ $product->category?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm mb-1">Cost</p>
                    <p class="text-lg font-semibold">${{ number_format($product->cost, 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm mb-1">Price</p>
                    <p class="text-lg font-semibold">${{ number_format($product->price, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
