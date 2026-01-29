@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('stock-outs.index') }}" class="hover:text-blue-600">Stock Out</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">Create New</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Create Stock Out</h2>
                <p class="text-sm text-gray-500 mt-1">Record outgoing stock from warehouse</p>
            </div>
            <a href="{{ route('stock-outs.index') }}"
                class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                <i class="ti ti-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </x-slot>

    <form action="{{ route('stock-outs.store') }}" method="POST" id="stockOutForm">
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
                                            {{ $warehouse->code }} - {{ $warehouse->name }}
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

                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Transaction Type <span class="text-red-500">*</span>
                                </label>
                                <select id="type" name="type" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('type') border-red-500 @enderror">
                                    <option value="">Select Type</option>
                                    <option value="SALES" {{ old('type') == 'SALES' ? 'selected' : '' }}>Sales</option>
                                    <option value="RETURN" {{ old('type') == 'RETURN' ? 'selected' : '' }}>Return</option>
                                    <option value="ADJUSTMENT" {{ old('type') == 'ADJUSTMENT' ? 'selected' : '' }}>Adjustment</option>
                                    <option value="PRODUCTION" {{ old('type') == 'PRODUCTION' ? 'selected' : '' }}>Production</option>
                                    <option value="DAMAGED" {{ old('type') == 'DAMAGED' ? 'selected' : '' }}>Damaged</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="status" name="status" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('status') border-red-500 @enderror">
                                    <option value="DRAFT" {{ old('status', 'DRAFT') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                                    <option value="PENDING" {{ old('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                                    <option value="APPROVED" {{ old('status') == 'APPROVED' ? 'selected' : '' }}>Approved</option>
                                    <option value="REJECTED" {{ old('status') == 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                                    <option value="CANCELLED" {{ old('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Customer Name -->
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Customer Name
                                </label>
                                <input type="text" id="customer_name" name="customer_name" 
                                    value="{{ old('customer_name') }}"
                                    placeholder="Enter customer name"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('customer_name') border-red-500 @enderror">
                                @error('customer_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reference Number -->
                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Reference Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="reference_number" name="reference_number" 
                                    value="{{ old('reference_number') }}" required
                                    placeholder="e.g., PO-001, SO-001"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('reference_number') border-red-500 @enderror">
                                @error('reference_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                            Products to Remove
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
                        <div id="empty-state" class="text-center py-12 text-gray-500">
                            <i class="ti ti-package-off text-6xl mb-4 opacity-20"></i>
                            <p class="text-lg font-medium mb-1">No items added yet</p>
                            <p class="text-sm">Click "Add Item" button to start adding products</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('stock-outs.index') }}"
                        class="inline-flex items-center px-6 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        <i class="ti ti-x mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        <i class="ti ti-device-floppy mr-2"></i>
                        Save Stock Out
                    </button>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden sticky top-6">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="ti ti-report-analytics mr-2 text-blue-600"></i>
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
                            <span id="total-quantity" class="text-lg font-bold text-gray-900">0.00</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Value</span>
                            <span id="total-value" class="text-lg font-bold text-blue-600">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Help -->
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="ti ti-info-circle text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-blue-900 mb-2">Quick Guide</h3>
                            <ul class="text-xs text-blue-800 space-y-1">
                                <li>• Fill in warehouse and date first</li>
                                <li>• Select appropriate transaction type</li>
                                <li>• Add reference number for tracking</li>
                                <li>• Add products with quantities</li>
                                <li>• Review summary before saving</li>
                                <li>• Draft status allows editing later</li>
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
                        <select name="items[${itemIndex}][product_id]" required 
                            class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            onchange="updateProductInfo(${itemIndex})">
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" 
                                    data-code="{{ $product->code }}"
                                    data-unit="{{ $product->unit->symbol ?? '' }}"
                                    data-cost="{{ $product->standard_cost }}">
                                    {{ $product->code }} - {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <select name="items[${itemIndex}][location_id]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">No Specific Location</option>
                            @foreach ($locations ?? [] as $location)
                                <option value="{{ $location->id }}">
                                    {{ $location->code }} - {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                        <input type="text" name="items[${itemIndex}][batch_number]" 
                            placeholder="Optional"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                        <input type="text" name="items[${itemIndex}][serial_number]" 
                            placeholder="For serial tracked items"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number" name="items[${itemIndex}][quantity]" step="0.01" min="0.01" required 
                            class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            onchange="calculateSubtotal(${itemIndex})">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Cost *</label>
                        <input type="number" name="items[${itemIndex}][unit_cost]" step="0.01" min="0" required 
                            class="item-cost w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            onchange="calculateSubtotal(${itemIndex})">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subtotal</label>
                        <input type="number" name="items[${itemIndex}][subtotal]" step="0.01" readonly 
                            class="item-subtotal w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-semibold">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <input type="text" name="items[${itemIndex}][notes]" 
                            placeholder="Additional notes for this item"
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

        function updateProductInfo(index) {
            const row = document.querySelector(`.item-row[data-index="${index}"]`);
            const select = row.querySelector('.product-select');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const cost = selectedOption.dataset.cost || 0;
                const costInput = row.querySelector('.item-cost');
                costInput.value = cost;
                calculateSubtotal(index);
            }
        }

        function calculateSubtotal(index) {
            const row = document.querySelector(`.item-row[data-index="${index}"]`);
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
            const subtotal = quantity * cost;
            
            row.querySelector('.item-subtotal').value = subtotal.toFixed(2);
            updateSummary();
        }

        function updateSummary() {
            const quantities = document.querySelectorAll('.item-quantity');
            const subtotals = document.querySelectorAll('.item-subtotal');

            let totalItems = quantities.length;
            let totalQuantity = 0;
            let totalValue = 0;

            quantities.forEach((qtyInput) => {
                const qty = parseFloat(qtyInput.value) || 0;
                totalQuantity += qty;
            });

            subtotals.forEach((subInput) => {
                const sub = parseFloat(subInput.value) || 0;
                totalValue += sub;
            });

            document.getElementById('total-items').textContent = totalItems;
            document.getElementById('total-quantity').textContent = totalQuantity.toFixed(2);
            document.getElementById('total-value').textContent = 'Rp ' + totalValue.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Form validation
        document.getElementById('stockOutForm').addEventListener('submit', function(e) {
            const itemsContainer = document.getElementById('items-container');
            if (itemsContainer.children.length === 0) {
                e.preventDefault();
                alert('Please add at least one item before saving.');
                return false;
            }
        });
    </script>
@endpush