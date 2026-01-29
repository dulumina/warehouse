@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('stock-ins.index') }}" class="hover:text-blue-600">Stock In</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">Create New</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Create Stock In</h2>
                <p class="text-sm text-gray-500 mt-1">Record incoming stock to warehouse</p>
            </div>
            <a href="{{ route('stock-ins.index') }}"
                class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                <i class="ti ti-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </x-slot>

    <form action="{{ route('stock-ins.store') }}" method="POST" id="stockInForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="ti ti-file-info mr-2 text-blue-600"></i>
                            General Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Warehouse -->
                            <div>
                                <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Warehouse <span class="text-red-500">*</span>
                                </label>
                                <select id="warehouse_id" name="warehouse_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('warehouse_id') border-red-500 @enderror">
                                    <option value="">Select Warehouse</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}"
                                            {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Transaction Date -->
                            <div>
                                <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Transaction Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="transaction_date" name="transaction_date"
                                    value="{{ old('transaction_date', date('Y-m-d')) }}" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('transaction_date') border-red-500 @enderror">
                                @error('transaction_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Supplier -->
                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Supplier
                                </label>
                                <select id="supplier_id" name="supplier_id"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('supplier_id') border-red-500 @enderror">
                                    <option value="">Select Supplier (Optional)</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Transaction Type <span class="text-red-500">*</span>
                                </label>
                                <select id="type" name="type" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('type') border-red-500 @enderror">
                                    <option value="">Select Type</option>
                                    <option value="PURCHASE" {{ old('type') == 'PURCHASE' ? 'selected' : '' }}>Purchase
                                    </option>
                                    <option value="RETURN" {{ old('type') == 'RETURN' ? 'selected' : '' }}>Return</option>
                                    <option value="ADJUSTMENT" {{ old('type') == 'ADJUSTMENT' ? 'selected' : '' }}>
                                        Adjustment</option>
                                    <option value="PRODUCTION" {{ old('type') == 'PRODUCTION' ? 'selected' : '' }}>
                                        Production</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reference Number -->
                            <div class="md:col-span-2">
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Reference Number
                                </label>
                                <input type="text" id="reference_number" name="reference_number"
                                    value="{{ old('reference_number') }}" placeholder="PO-2024-001"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('reference_number') border-red-500 @enderror">
                                @error('reference_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Purchase Order number or other reference</p>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <textarea id="notes" name="notes" rows="3" placeholder="Additional notes..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="ti ti-package mr-2 text-blue-600"></i>
                            Items
                        </h3>
                        <button type="button" onclick="addItem()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <i class="ti ti-plus mr-2"></i>
                            Add Item
                        </button>
                    </div>
                    <div class="p-6">
                        <div id="items-container" class="space-y-4">
                            <!-- Items will be added here dynamically -->
                        </div>

                        <div id="empty-state" class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ti ti-package-off text-3xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium mb-2">No items added yet</p>
                            <p class="text-sm text-gray-400 mb-4">Click "Add Item" button to start adding products</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Summary -->
            <div class="space-y-6">
                <!-- Summary Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden sticky top-6">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="ti ti-receipt mr-2 text-blue-600"></i>
                            Summary
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Total Items</span>
                            <span id="total-items" class="text-lg font-bold text-gray-900">0</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Total Quantity</span>
                            <span id="total-quantity" class="text-lg font-bold text-gray-900">0</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-base font-semibold text-gray-900">Total Value</span>
                            <span id="total-value" class="text-xl font-bold text-blue-600">$0.00</span>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 space-y-3">
                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-6 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <i class="ti ti-device-floppy mr-2"></i>
                            Save Stock In
                        </button>
                        <a href="{{ route('stock-ins.index') }}"
                            class="w-full inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <i class="ti ti-x mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Quick Help -->
                <div class="bg-blue-50 rounded-xl border border-blue-100 p-5">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="ti ti-info-circle text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-semibold text-blue-900 mb-1">Quick Help</h4>
                            <ul class="text-xs text-blue-800 space-y-1">
                                <li>• Fill in warehouse and date first</li>
                                <li>• Add products with quantities</li>
                                <li>• Review summary before saving</li>
                                <li>• Draft can be edited later</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        let itemIndex = 0;

        function addItem() {
            itemIndex++;
            const container = document.getElementById('items-container');
            const emptyState = document.getElementById('empty-state');

            emptyState.classList.add('hidden');

            const itemHtml = `
            <div class="item-row border border-gray-200 rounded-lg p-4 bg-gray-50/50" data-index="${itemIndex}">
                <div class="flex justify-between items-start mb-4">
                    <h4 class="text-sm font-semibold text-gray-700">Item #${itemIndex}</h4>
                    <button type="button" onclick="removeItem(${itemIndex})" class="text-red-600 hover:text-red-800">
                        <i class="ti ti-trash text-lg"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product *</label>
                        <select name="items[${itemIndex}][product_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-cost="{{ $product->standard_cost }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number" name="items[${itemIndex}][quantity]" step="0.01" min="0.01" required 
                            class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            onchange="updateSummary()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Cost *</label>
                        <input type="number" name="items[${itemIndex}][unit_cost]" step="0.01" min="0" required 
                            class="item-cost w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            onchange="updateSummary()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                        <input type="text" name="items[${itemIndex}][batch_number]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                        <input type="date" name="items[${itemIndex}][expiry_date]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <input type="text" name="items[${itemIndex}][notes]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        `;

            container.insertAdjacentHTML('beforeend', itemHtml);
            updateSummary();
        }

        function removeItem(index) {
            const item = document.querySelector(`.item-row[data-index="${index}"]`);
            if (item) {
                item.remove();
                updateSummary();

                const container = document.getElementById('items-container');
                const emptyState = document.getElementById('empty-state');
                if (container.children.length === 0) {
                    emptyState.classList.remove('hidden');
                }
            }
        }

        function updateSummary() {
            const quantities = document.querySelectorAll('.item-quantity');
            const costs = document.querySelectorAll('.item-cost');

            let totalItems = quantities.length;
            let totalQuantity = 0;
            let totalValue = 0;

            quantities.forEach((qtyInput, index) => {
                const qty = parseFloat(qtyInput.value) || 0;
                const cost = parseFloat(costs[index].value) || 0;

                totalQuantity += qty;
                totalValue += qty * cost;
            });

            document.getElementById('total-items').textContent = totalItems;
            document.getElementById('total-quantity').textContent = totalQuantity.toFixed(2);
            document.getElementById('total-value').textContent = '$' + totalValue.toFixed(2);
        }

        // Auto-fill unit cost when product is selected
        document.addEventListener('change', function(e) {
            if (e.target.name && e.target.name.includes('[product_id]')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const cost = selectedOption.getAttribute('data-cost');
                const itemRow = e.target.closest('.item-row');
                const costInput = itemRow.querySelector('.item-cost');
                if (cost && costInput) {
                    costInput.value = cost;
                    updateSummary();
                }
            }
        });

        // Form validation
        document.getElementById('stockInForm').addEventListener('submit', function(e) {
            const itemsContainer = document.getElementById('items-container');
            if (itemsContainer.children.length === 0) {
                e.preventDefault();
                alert('Please add at least one item before saving.');
                return false;
            }
        });
    </script>
@endpush