@extends('layouts.modernize')

@section('content')
    <div class="container-fluid px-4 py-6">
        <!-- Breadcrumb -->
        <nav class="flex text-sm text-gray-600 mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="{{ route('products.index') }}" class="hover:text-blue-600 transition-colors">
                        <i class="ti ti-home text-lg"></i>
                    </a>
                </li>
                <li><i class="ti ti-chevron-right text-xs"></i></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-blue-600 transition-colors">Products</a></li>
                <li><i class="ti ti-chevron-right text-xs"></i></li>
                <li class="text-gray-900 font-medium">Product Details</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $product->type == 'raw' ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $product->type == 'finished' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $product->type == 'service' ? 'bg-blue-100 text-blue-700' : '' }}">
                        {{ ucfirst($product->type) }}
                    </span>
                </div>
                <p class="text-gray-600">Product Code: <span class="font-mono font-semibold">{{ $product->code }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('products.edit', $product) }}" 
                   class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all inline-flex items-center gap-2">
                    <i class="ti ti-edit"></i>
                    Edit Product
                </a>
                <a href="{{ route('products.index') }}" 
                   class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition-all inline-flex items-center gap-2">
                    <i class="ti ti-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content - 2/3 width -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-600 rounded-lg p-2">
                                <i class="ti ti-info-circle text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Basic Information</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Product Code</p>
                                <p class="text-lg font-mono font-semibold text-gray-900">{{ $product->code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Product Name</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $product->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Category</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $product->category?->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Unit</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $product->unit?->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Product Type</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $product->type == 'raw' ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $product->type == 'finished' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $product->type == 'service' ? 'bg-blue-100 text-blue-700' : '' }}">
                                    {{ ucfirst($product->type) }}
                                </span>
                            </div>
                            @if($product->weight)
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Weight</p>
                                <p class="text-lg font-semibold text-gray-900">{{ number_format($product->weight, 2) }} kg</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-600 rounded-lg p-2">
                                <i class="ti ti-coin text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Pricing Information</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                                <p class="text-sm text-red-700 mb-1 font-medium">Cost Price</p>
                                <p class="text-2xl font-bold text-red-900">Rp {{ number_format($product->cost, 2) }}</p>
                                <p class="text-xs text-red-600 mt-1">Base purchase cost</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                                <p class="text-sm text-green-700 mb-1 font-medium">Selling Price</p>
                                <p class="text-2xl font-bold text-green-900">Rp {{ number_format($product->price, 2) }}</p>
                                <p class="text-xs text-green-600 mt-1">Customer price</p>
                            </div>
                        </div>
                        
                        @php
                            $margin = $product->price - $product->cost;
                            $marginPercent = $product->cost > 0 ? ($margin / $product->cost) * 100 : 0;
                        @endphp
                        
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-blue-700 font-medium mb-1">Profit Margin</p>
                                    <p class="text-xl font-bold text-blue-900">Rp {{ number_format($margin, 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-blue-700 font-medium mb-1">Margin %</p>
                                    <p class="text-xl font-bold text-blue-900">{{ number_format($marginPercent, 1) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Limits -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                        <div class="flex items-center gap-3">
                            <div class="bg-purple-600 rounded-lg p-2">
                                <i class="ti ti-package text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Stock Limits</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border border-orange-200 rounded-lg p-4 bg-orange-50">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="bg-orange-600 rounded-lg p-2">
                                        <i class="ti ti-arrow-down text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-orange-700 font-medium">Minimum Stock</p>
                                        <p class="text-xs text-orange-600">Reorder threshold</p>
                                    </div>
                                </div>
                                <p class="text-3xl font-bold text-orange-900 mt-2">{{ number_format($product->min_stock, 2) }}</p>
                            </div>
                            
                            <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="bg-green-600 rounded-lg p-2">
                                        <i class="ti ti-arrow-up text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-green-700 font-medium">Maximum Stock</p>
                                        <p class="text-xs text-green-600">Storage capacity</p>
                                    </div>
                                </div>
                                <p class="text-3xl font-bold text-green-900 mt-2">{{ number_format($product->max_stock, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($product->description)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="bg-gray-600 rounded-lg p-2">
                                <i class="ti ti-notes text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Description</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar - 1/3 width -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-chart-bar text-gray-700 text-lg"></i>
                            <h4 class="font-bold text-gray-900">Quick Stats</h4>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Category</span>
                                <span class="font-semibold text-gray-900">{{ $product->category?->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Unit</span>
                                <span class="font-semibold text-gray-900">{{ $product->unit?->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Type</span>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                    {{ $product->type == 'raw' ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $product->type == 'finished' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $product->type == 'service' ? 'bg-blue-100 text-blue-700' : '' }}">
                                    {{ ucfirst($product->type) }}
                                </span>
                            </div>
                            @if($product->weight)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Weight</span>
                                <span class="font-semibold text-gray-900">{{ number_format($product->weight, 2) }} kg</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-clock text-gray-700 text-lg"></i>
                            <h4 class="font-bold text-gray-900">Record Info</h4>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-500 mb-1">Created At</p>
                                <p class="font-semibold text-gray-900">{{ $product->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <p class="text-gray-500 mb-1">Last Updated</p>
                                <p class="font-semibold text-gray-900">{{ $product->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-settings text-gray-700 text-lg"></i>
                            <h4 class="font-bold text-gray-900">Actions</h4>
                        </div>
                    </div>
                    <div class="p-4 space-y-2">
                        <a href="{{ route('products.edit', $product) }}" 
                           class="w-full px-4 py-2.5 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all inline-flex items-center gap-2">
                            <i class="ti ti-edit"></i>
                            Edit Product
                        </a>
                        <button type="button" 
                                onclick="if(confirm('Are you sure you want to delete this product? This action cannot be undone.')) document.getElementById('deleteForm').submit()"
                                class="w-full px-4 py-2.5 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-all inline-flex items-center gap-2">
                            <i class="ti ti-trash"></i>
                            Delete Product
                        </button>
                        <a href="{{ route('products.index') }}" 
                           class="w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all inline-flex items-center gap-2">
                            <i class="ti ti-list"></i>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" 
          action="{{ route('products.destroy', $product) }}" 
          method="POST" 
          class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endsection