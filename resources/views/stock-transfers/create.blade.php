@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('stock-transfers.index') }}" class="hover:text-blue-600">Stock Transfer</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">Create New</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Create Stock Transfer</h2>
                <p class="text-sm text-gray-500 mt-1">Transfer stock between warehouses</p>
            </div>
            <a href="{{ route('stock-transfers.index') }}"
                class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                <i class="ti ti-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </x-slot>

    <form action="{{ route('stock-transfers.store') }}" method="POST" id="stockTransferForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="ti ti-file-info mr-2 text-blue-600"></i>
                            Transfer Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- From Warehouse -->
                            <div>
                                <label for="from_warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    From Warehouse <span class="text-red-500">*</span>
                                </label>
                                <select id="from_warehouse_id" name="from_warehouse_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('from_warehouse_id') border-red-500 @enderror"
                                    onchange="loadLocations('from')">
                                    <option value="">Select Source Warehouse</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}"
                                            {{ old('from_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->code }} - {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_warehouse_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- To Warehouse -->
                            <div>
                                <label for="to_warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    To Warehouse <span class="text-red-500">*</span>
                                </label>
                                <select id="to_warehouse_id" name="to_warehouse_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('to_warehouse_id') border-red-500 @enderror"
                                    onchange="loadLocations('to')">
                                    <option value="">Select Destination Warehouse</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}"
                                            {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->code }} - {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_warehouse_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Transaction Date -->
                            <div>
                                <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Transfer Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="transaction_date" name="transaction_date"
                                    value="{{ old('transaction_date', date('Y-m-d')) }}" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('transaction_date') border-red-500 @enderror">
                                @error('transaction_date')
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
                                    <option value="IN_TRANSIT" {{ old('status') == 'IN_TRANSIT' ? 'selected' : '' }}>In Transit</option>
                                    <option value="RECEIVED" {{ old('status') == 'RECEIVED' ? 'selected' : '' }}>Received</option>
                                    <option value="REJECTED" {{ old('status') == 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                                    <option value="CANCELLED" {{ old('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
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
                            <textarea id="notes" name="notes" rows="3" placeholder="Additional notes about this transfer..."
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
                            Transfer Items
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
                            <p class="text-sm">Click "Add Item" button to start adding products to transfer</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('stock-transfers.index') }}"
                        class="inline-flex items-center px-6 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        <i class="ti ti-x mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        <i class="ti ti-device-floppy mr-2"></i>
                        Save Transfer
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
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Quantity</span>
                            <span id="total-quantity" class="text-lg font-bold text-blue-600">0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Transfer Route -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="ti ti-route text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-semibold text-blue-900 mb-3">Transfer Route</h3>
                            <div class="space-y-2">
                                <div class="flex items-center text-xs text-blue-800">
                                    <i class="ti ti-building-warehouse mr-2"></i>
                                    <span id="from-warehouse-display" class="font-medium">Select source warehouse</span>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="ti ti-arrow-down text-blue-600"></i>
                                </div>
                                <div class="flex items-center text-xs text-blue-800">
                                    <i class="ti ti-building-warehouse mr-2"></i>
                                    <span id="to-warehouse-display" class="font-medium">Select destination warehouse</span>
                                </div>
                            </div>
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
                                <li>• Select source and destination warehouses</li>
                                <li>• Warehouses must be different</li>
                                <li>• Add products with quantities to transfer</li>
                                <li>• Specify locations for better tracking</li>
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
        const fromLocations = @json($locations);
        const toLocations   = @json($locations);

        function loadLocations(type) {
            const warehouseId = document.getElementById(type + '_warehouse_id').value;
            
            if (type === 'from') {
                const warehouseName = document.querySelector('#from_warehouse_id option:checked').text;
                document.getElementById('from-warehouse-display').textContent = warehouseName || 'Select source warehouse';
            } else {
                const warehouseName = document.querySelector('#to_warehouse_id option:checked').text;
                document.getElementById('to-warehouse-display').textContent = warehouseName || 'Select destination warehouse';
            }
        }

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
                            class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" 
                                    data-code="{{ $product->code }}"
                                    data-unit="{{ $product->unit->symbol ?? '' }}">
                                    {{ $product->code }} - {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Location</label>
                        <select name="items[${itemIndex}][from_location_id]" 
                            class="from-location-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">No Specific Location</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Location</label>
                        <select name="items[${itemIndex}][to_location_id]" 
                            class="to-location-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number" name="items[${itemIndex}][quantity]" step="0.01" min="0.01" required 
                            class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            onchange="updateSummary()">
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
            updateLocationSelects(itemIndex);
            updateSummary();
        }

        function updateLocationSelects(index) {
            const fromWarehouseId = document.getElementById('from_warehouse_id').value;
            const toWarehouseId = document.getElementById('to_warehouse_id').value;
            
            const row = document.querySelector(`.item-row[data-index="${index}"]`);
            const fromLocationSelect = row.querySelector('.from-location-select');
            const toLocationSelect = row.querySelector('.to-location-select');

            // Update from location
            fromLocationSelect.innerHTML = '<option value="">No Specific Location</option>';
            if (fromWarehouseId) {
                fromLocations.filter(l => l.warehouse_id === fromWarehouseId).forEach(loc => {
                    fromLocationSelect.innerHTML += `<option value="${loc.id}">${loc.code} - ${loc.name}</option>`;
                });
            }

            // Update to location
            toLocationSelect.innerHTML = '<option value="">No Specific Location</option>';
            if (toWarehouseId) {
                toLocations.filter(l => l.warehouse_id === toWarehouseId).forEach(loc => {
                    toLocationSelect.innerHTML += `<option value="${loc.id}">${loc.code} - ${loc.name}</option>`;
                });
            }
        }

        // Update all location selects when warehouse changes
        document.getElementById('from_warehouse_id').addEventListener('change', function() {
            document.querySelectorAll('.item-row').forEach(row => {
                const index = row.dataset.index;
                updateLocationSelects(index);
            });
        });

        document.getElementById('to_warehouse_id').addEventListener('change', function() {
            document.querySelectorAll('.item-row').forEach(row => {
                const index = row.dataset.index;
                updateLocationSelects(index);
            });
        });

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

            let totalItems = quantities.length;
            let totalQuantity = 0;

            quantities.forEach((qtyInput) => {
                const qty = parseFloat(qtyInput.value) || 0;
                totalQuantity += qty;
            });

            document.getElementById('total-items').textContent = totalItems;
            document.getElementById('total-quantity').textContent = totalQuantity.toFixed(2);
        }

        // Form validation
        document.getElementById('stockTransferForm').addEventListener('submit', function(e) {
            const fromWarehouse = document.getElementById('from_warehouse_id').value;
            const toWarehouse = document.getElementById('to_warehouse_id').value;
            const itemsContainer = document.getElementById('items-container');

            if (fromWarehouse === toWarehouse) {
                e.preventDefault();
                alert('Source and destination warehouses must be different.');
                return false;
            }

            if (itemsContainer.children.length === 0) {
                e.preventDefault();
                alert('Please add at least one item before saving.');
                return false;
            }
        });
    </script>
@endpush