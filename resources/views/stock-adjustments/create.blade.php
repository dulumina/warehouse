@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('stock-adjustments.index') }}" class="hover:text-blue-600">Stock Adjustment</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">Create New</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Create Stock Adjustment</h2>
                <p class="text-sm text-gray-500 mt-1">Adjust inventory levels to match physical count</p>
            </div>
            <a href="{{ route('stock-adjustments.index') }}"
                class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                <i class="ti ti-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </x-slot>

    <form action="{{ route('stock-adjustments.store') }}" method="POST" id="stockAdjustmentForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="ti ti-file-info mr-2 text-blue-600"></i>
                            Adjustment Information
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
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('warehouse_id') border-red-500 @enderror"
                                    onchange="loadLocations()">
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

                            <!-- Adjustment Date -->
                            <div>
                                <label for="adjustment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Adjustment Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="adjustment_date" name="adjustment_date"
                                    value="{{ old('adjustment_date', date('Y-m-d')) }}" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('adjustment_date') border-red-500 @enderror">
                                @error('adjustment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Adjustment Type <span class="text-red-500">*</span>
                                </label>
                                <select id="type" name="type" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('type') border-red-500 @enderror">
                                    <option value="">Select Type</option>
                                    <option value="PHYSICAL_COUNT" {{ old('type') == 'PHYSICAL_COUNT' ? 'selected' : '' }}>Physical Count</option>
                                    <option value="CORRECTION" {{ old('type') == 'CORRECTION' ? 'selected' : '' }}>Correction</option>
                                    <option value="DAMAGED" {{ old('type') == 'DAMAGED' ? 'selected' : '' }}>Damaged Goods</option>
                                    <option value="EXPIRED" {{ old('type') == 'EXPIRED' ? 'selected' : '' }}>Expired Items</option>
                                    <option value="FOUND" {{ old('type') == 'FOUND' ? 'selected' : '' }}>Found Items</option>
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
                                    <option value="APPROVED" {{ old('status') == 'APPROVED' ? 'selected' : '' }}>Approved</option>
                                    <option value="REJECTED" {{ old('status') == 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <textarea id="notes" name="notes" rows="3" placeholder="Reason for adjustment or additional notes..."
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
                            <i class="ti ti-adjustments-horizontal mr-2 text-blue-600"></i>
                            Adjustment Items
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
                            <i class="ti ti-adjustments-off text-6xl mb-4 opacity-20"></i>
                            <p class="text-lg font-medium mb-1">No items added yet</p>
                            <p class="text-sm">Click "Add Item" button to start adding products to adjust</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('stock-adjustments.index') }}"
                        class="inline-flex items-center px-6 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        <i class="ti ti-x mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        <i class="ti ti-device-floppy mr-2"></i>
                        Save Adjustment
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
                            <span class="text-sm text-gray-600">Positive Adj.</span>
                            <span id="positive-count" class="text-lg font-bold text-green-600">0</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Negative Adj.</span>
                            <span id="negative-count" class="text-lg font-bold text-red-600">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Value Impact</span>
                            <span id="value-impact" class="text-lg font-bold text-blue-600">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Help -->
                <div class="bg-amber-50 rounded-xl p-6 border border-amber-100">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="ti ti-info-circle text-2xl text-amber-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-amber-900 mb-2">Quick Guide</h3>
                            <ul class="text-xs text-amber-800 space-y-1">
                                <li>• Select warehouse and adjustment type</li>
                                <li>• Add products that need adjustment</li>
                                <li>• Enter system quantity (current stock)</li>
                                <li>• Enter actual quantity (physical count)</li>
                                <li>• Difference is calculated automatically</li>
                                <li>• Provide reason for each adjustment</li>
                                <li>• Draft can be reviewed before approval</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Adjustment Types Info -->
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="ti ti-list-check text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-blue-900 mb-2">Adjustment Types</h3>
                            <div class="text-xs text-blue-800 space-y-2">
                                <div>
                                    <p class="font-semibold">Physical Count</p>
                                    <p class="text-blue-700">Regular stock counting reconciliation</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Correction</p>
                                    <p class="text-blue-700">Fix data entry errors</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Damaged</p>
                                    <p class="text-blue-700">Remove damaged inventory</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Expired</p>
                                    <p class="text-blue-700">Remove expired items</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Found</p>
                                    <p class="text-blue-700">Add discovered inventory</p>
                                </div>
                            </div>
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
        const warehouseLocations = @json($warehouseLocations);

        function loadLocations() {
            const warehouseId = document.getElementById('warehouse_id').value;
            
            document.querySelectorAll('.item-row').forEach(row => {
                const index = row.dataset.index;
                updateLocationSelect(index, warehouseId);
            });
        }

        function updateLocationSelect(index, warehouseId) {
            const row = document.querySelector(`.item-row[data-index="${index}"]`);
            if (!row) return;
            
            const locationSelect = row.querySelector('.location-select');
            locationSelect.innerHTML = '<option value="">No Specific Location</option>';
            
            if (warehouseId) {
                warehouseLocations.filter(l => l.warehouse_id === warehouseId).forEach(loc => {
                    locationSelect.innerHTML += `<option value="${loc.id}">${loc.code} - ${loc.name}</option>`;
                });
            }
        }

        function addItem() {
            itemIndex++;
            const container = document.getElementById('items-container');
            const emptyState = document.getElementById('empty-state');
            const warehouseId = document.getElementById('warehouse_id').value;

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
                            class="location-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">No Specific Location</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                        <input type="text" name="items[${itemIndex}][batch_number]" 
                            placeholder="Optional"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">System Quantity *</label>
                        <input type="number" name="items[${itemIndex}][system_quantity]" step="0.01" min="0" required 
                            class="system-qty w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Current stock"
                            onchange="calculateDifference(${itemIndex})">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Actual Quantity *</label>
                        <input type="number" name="items[${itemIndex}][actual_quantity]" step="0.01" min="0" required 
                            class="actual-qty w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Physical count"
                            onchange="calculateDifference(${itemIndex})">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Difference</label>
                        <input type="number" name="items[${itemIndex}][difference]" step="0.01" readonly 
                            class="difference-qty w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 font-semibold">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Cost *</label>
                        <input type="number" name="items[${itemIndex}][unit_cost]" step="0.01" min="0" required 
                            class="unit-cost w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            onchange="calculateDifference(${itemIndex})">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Value Difference</label>
                        <input type="number" name="items[${itemIndex}][value_difference]" step="0.01" readonly 
                            class="value-diff w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-lg font-bold">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                        <input type="text" name="items[${itemIndex}][reason]" 
                            placeholder="Explain the reason for this adjustment"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        `;

            container.insertAdjacentHTML('beforeend', itemHtml);
            
            if (warehouseId) {
                updateLocationSelect(itemIndex, warehouseId);
            }
            
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
                const costInput = row.querySelector('.unit-cost');
                costInput.value = cost;
                calculateDifference(index);
            }
        }

        function calculateDifference(index) {
            const row = document.querySelector(`.item-row[data-index="${index}"]`);
            const systemQty = parseFloat(row.querySelector('.system-qty').value) || 0;
            const actualQty = parseFloat(row.querySelector('.actual-qty').value) || 0;
            const unitCost = parseFloat(row.querySelector('.unit-cost').value) || 0;
            
            const difference = actualQty - systemQty;
            const valueDiff = difference * unitCost;
            
            row.querySelector('.difference-qty').value = difference.toFixed(4);
            row.querySelector('.value-diff').value = valueDiff.toFixed(2);
            
            // Color code the difference
            const diffInput = row.querySelector('.difference-qty');
            if (difference > 0) {
                diffInput.classList.remove('text-red-600');
                diffInput.classList.add('text-green-600');
            } else if (difference < 0) {
                diffInput.classList.remove('text-green-600');
                diffInput.classList.add('text-red-600');
            } else {
                diffInput.classList.remove('text-green-600', 'text-red-600');
            }
            
            updateSummary();
        }

        function updateSummary() {
            const rows = document.querySelectorAll('.item-row');
            let totalItems = rows.length;
            let positiveCount = 0;
            let negativeCount = 0;
            let totalValueImpact = 0;

            rows.forEach(row => {
                const diff = parseFloat(row.querySelector('.difference-qty').value) || 0;
                const valueDiff = parseFloat(row.querySelector('.value-diff').value) || 0;
                
                if (diff > 0) positiveCount++;
                if (diff < 0) negativeCount++;
                
                totalValueImpact += valueDiff;
            });

            document.getElementById('total-items').textContent = totalItems;
            document.getElementById('positive-count').textContent = positiveCount;
            document.getElementById('negative-count').textContent = negativeCount;
            
            const impactSpan = document.getElementById('value-impact');
            impactSpan.textContent = 'Rp ' + totalValueImpact.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            
            // Color code the impact
            if (totalValueImpact > 0) {
                impactSpan.classList.remove('text-red-600');
                impactSpan.classList.add('text-green-600');
            } else if (totalValueImpact < 0) {
                impactSpan.classList.remove('text-green-600');
                impactSpan.classList.add('text-red-600');
            } else {
                impactSpan.classList.remove('text-green-600', 'text-red-600');
                impactSpan.classList.add('text-blue-600');
            }
        }

        // Form validation
        document.getElementById('stockAdjustmentForm').addEventListener('submit', function(e) {
            const itemsContainer = document.getElementById('items-container');
            if (itemsContainer.children.length === 0) {
                e.preventDefault();
                alert('Please add at least one item before saving.');
                return false;
            }
        });
    </script>
@endpush