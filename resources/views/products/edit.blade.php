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
                <li class="text-gray-900 font-medium">Edit Product</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-3xl font-bold text-gray-900">Edit Product</h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
                    <i class="ti ti-edit text-base"></i>
                    Editing Mode
                </span>
            </div>
            <p class="text-gray-600">
                Updating information for: <strong class="text-gray-900">{{ $product->name }}</strong>
                <span class="text-gray-400 mx-2">•</span>
                <span class="text-gray-500">{{ $product->code }}</span>
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form - 2/3 width -->
            <div class="lg:col-span-2">
                <form action="{{ route('products.update', $product) }}" method="POST" id="productForm">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-blue-600 rounded-lg p-2">
                                        <i class="ti ti-info-circle text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">Basic Information</h3>
                                        <p class="text-sm text-gray-600">Product identification details</p>
                                    </div>
                                </div>
                                <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded">Core Info</span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Product Code -->
                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Product Code <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-barcode text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                            id="code" 
                                            name="code" 
                                            value="{{ old('code', $product->code) }}" 
                                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('code') border-red-500 @enderror"
                                            placeholder="e.g., PROD-001"
                                            required>
                                    </div>
                                    @error('code')
                                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                            <i class="ti ti-alert-circle"></i>{{ $message }}
                                        </p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Unique identifier for the product</p>
                                </div>

                                <!-- Product Name -->
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Product Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-package text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                            id="name" 
                                            name="name" 
                                            value="{{ old('name', $product->name) }}" 
                                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                                            placeholder="e.g., Premium Coffee Beans"
                                            required>
                                    </div>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                            <i class="ti ti-alert-circle"></i>{{ $message }}
                                        </p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Full name of the product</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Classification & Unit Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-purple-600 rounded-lg p-2">
                                        <i class="ti ti-category text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">Classification & Unit</h3>
                                        <p class="text-sm text-gray-600">Product categorization and measurement</p>
                                    </div>
                                </div>
                                <span class="text-xs font-medium text-purple-700 bg-white px-2 py-1 rounded">Classification</span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Category -->
                                <div>
                                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Category <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-category-2 text-gray-400"></i>
                                        </div>
                                        <select id="category_id" 
                                                name="category_id" 
                                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('category_id') border-red-500 @enderror"
                                                required>
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('category_id')
                                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                            <i class="ti ti-alert-circle"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Unit -->
                                <div>
                                    <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Unit <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-ruler text-gray-400"></i>
                                        </div>
                                        <select id="unit_id" 
                                                name="unit_id" 
                                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('unit_id') border-red-500 @enderror"
                                                required>
                                            <option value="">Select Unit</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('unit_id')
                                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                            <i class="ti ti-alert-circle"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Type -->
                                <div>
                                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Type <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-tag text-gray-400"></i>
                                        </div>
                                        <select id="type" 
                                                name="type" 
                                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                                required>
                                            <option value="FINISHED_GOOD" {{ old('type', $product->type) == 'FINISHED_GOOD' ? 'selected' : '' }}>Finished Good</option>
                                            <option value="RAW_MATERIAL" {{ old('type', $product->type) == 'RAW_MATERIAL' ? 'selected' : '' }}>Raw Material</option>
                                            <option value="CONSUMABLE" {{ old('type', $product->type) == 'CONSUMABLE' ? 'selected' : '' }}>Consumable</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Stock Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-green-600 rounded-lg p-2">
                                        <i class="ti ti-coin text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">Pricing & Stock Limits</h3>
                                        <p class="text-sm text-gray-600">Cost, price, and inventory thresholds</p>
                                    </div>
                                </div>
                                <span class="text-xs font-medium text-green-700 bg-white px-2 py-1 rounded">Financial</span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <!-- Standard Cost -->
                                <div>
                                    <label for="standard_cost" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Standard Cost <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-medium">Rp</span>
                                        </div>
                                        <input type="number" 
                                            id="standard_cost" 
                                            name="standard_cost" 
                                            step="0.01" 
                                            value="{{ old('standard_cost', $product->standard_cost) }}" 
                                            class="pl-12 w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 @error('standard_cost') border-red-500 @enderror"
                                            placeholder="0.00"
                                            required>
                                    </div>
                                    @error('standard_cost')
                                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                            <i class="ti ti-alert-circle"></i>{{ $message }}
                                        </p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Base cost price</p>
                                </div>

                                <!-- Selling Price -->
                                <div>
                                    <label for="selling_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Selling Price <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-medium">Rp</span>
                                        </div>
                                        <input type="number" 
                                            id="selling_price" 
                                            name="selling_price" 
                                            step="0.01" 
                                            value="{{ old('selling_price', $product->selling_price) }}" 
                                            class="pl-12 w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 @error('selling_price') border-red-500 @enderror"
                                            placeholder="0.00"
                                            required>
                                    </div>
                                    @error('selling_price')
                                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                            <i class="ti ti-alert-circle"></i>{{ $message }}
                                        </p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Customer price</p>
                                </div>

                                <!-- Min Stock -->
                                <div>
                                    <label for="min_stock" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Min Stock <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-arrow-down text-gray-400"></i>
                                        </div>
                                        <input type="number" 
                                            id="min_stock" 
                                            name="min_stock" 
                                            step="0.01" 
                                            value="{{ old('min_stock', $product->min_stock) }}" 
                                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                            placeholder="0"
                                            required>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Reorder threshold</p>
                                </div>

                                <!-- Max Stock -->
                                <div>
                                    <label for="max_stock" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Max Stock <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-arrow-up text-gray-400"></i>
                                        </div>
                                        <input type="number" 
                                            id="max_stock" 
                                            name="max_stock" 
                                            step="0.01" 
                                            value="{{ old('max_stock', $product->max_stock) }}" 
                                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                            placeholder="0"
                                            required>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Maximum capacity</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-6 py-4 border-b border-orange-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-orange-600 rounded-lg p-2">
                                        <i class="ti ti-notes text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">Additional Details</h3>
                                        <p class="text-sm text-gray-600">Optional product information</p>
                                    </div>
                                </div>
                                <span class="text-xs font-medium text-orange-700 bg-white px-2 py-1 rounded">Optional</span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-6">
                                <!-- Weight -->
                                <div>
                                    <label for="weight" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Weight (kg)
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-weight text-gray-400"></i>
                                        </div>
                                        <input type="number" 
                                            id="weight" 
                                            name="weight" 
                                            step="0.01" 
                                            value="{{ old('weight', $product->weight) }}" 
                                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50"
                                            placeholder="0.00">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Product weight for shipping calculations</p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Description
                                    </label>
                                    <textarea id="description" 
                                            name="description" 
                                            rows="4"
                                            class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50"
                                            placeholder="Detailed product description, features, specifications...">{{ old('description', $product->description) }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Provide a comprehensive description of the product</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600 flex items-center gap-1.5">
                                <i class="ti ti-info-circle"></i>
                                Make sure all changes are correct
                            </p>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('products.index') }}" 
                                   class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition-all inline-flex items-center gap-2">
                                    <i class="ti ti-x"></i>
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all inline-flex items-center gap-2">
                                    <i class="ti ti-device-floppy"></i>
                                    Update Product
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sidebar - 1/3 width -->
            <div class="lg:col-span-1">
                <!-- Current Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-info-circle text-blue-600 text-lg"></i>
                            <h4 class="font-bold text-gray-900">Current Information</h4>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Product Code</p>
                            <p class="font-semibold text-gray-900">{{ $product->code }}</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Product Name</p>
                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Category</p>
                            <p class="font-semibold text-gray-900">{{ $product->category?->name ?? '-' }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-2">Pricing</p>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Cost:</span>
                                <span class="font-semibold">Rp {{ number_format($product->cost, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Price:</span>
                                <span class="font-semibold">Rp {{ number_format($product->price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warning Card -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="ti ti-alert-triangle text-yellow-600 text-xl mt-0.5"></i>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-2">Important Notes</h4>
                            <ul class="space-y-1.5 text-sm text-gray-700">
                                <li class="flex items-start gap-2">
                                    <span class="text-yellow-600 mt-0.5">•</span>
                                    <span>Changes affect inventory records</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-yellow-600 mt-0.5">•</span>
                                    <span>Code changes may impact reports</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-yellow-600 mt-0.5">•</span>
                                    <span>Price affects margin calculations</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-bolt text-blue-600 text-lg"></i>
                            <h4 class="font-bold text-gray-900">Quick Actions</h4>
                        </div>
                    </div>
                    <div class="p-4 space-y-2">
                        <a href="{{ route('products.show', $product) }}" 
                           class="w-full px-4 py-2.5 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all inline-flex items-center gap-2">
                            <i class="ti ti-eye"></i>
                            View Product Details
                        </a>
                        <button type="button" 
                                onclick="if(confirm('Are you sure you want to delete this product?')) document.getElementById('deleteForm').submit()"
                                class="w-full px-4 py-2.5 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-all inline-flex items-center gap-2">
                            <i class="ti ti-trash"></i>
                            Delete Product
                        </button>
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

    @push('scripts')
    <script>
        // Form validation
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });

        // Remove error styling on input
        document.querySelectorAll('input[required], select[required]').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('border-red-500');
                }
            });
        });

        // Track changes
        let hasChanges = false;
        const originalValues = new Map();
        
        document.querySelectorAll('input, select, textarea').forEach(input => {
            originalValues.set(input.name, input.value);
            
            input.addEventListener('change', function() {
                hasChanges = this.value !== originalValues.get(this.name);
            });
        });

        // Warn before leaving if there are unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (hasChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Don't warn when form is submitted
        document.getElementById('productForm').addEventListener('submit', function() {
            hasChanges = false;
        });

        // Price validation
        const costInput = document.getElementById('standard_cost');
        const priceInput = document.getElementById('selling_price');

        priceInput.addEventListener('blur', function() {
            const cost = parseFloat(costInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;

            if (price < cost) {
                alert('Warning: Selling price is lower than standard cost!');
            }
        });
    </script>
    @endpush
@endsection