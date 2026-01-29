<x-modernize-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold leading-tight text-gray-800">
                    {{ __('Dashboard Overview') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">Welcome back, {{ auth()->user()->name }}! Here's what's happening today.</p>
            </div>
            <div class="flex space-x-3">
                <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded-full">
                    <i class="mr-1 ti ti-calendar"></i>
                    {{ now()->format('D, d M Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-2">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Products -->
            <div class="p-5 transition-all duration-300 bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl text-blue-600">
                        <i class="text-2xl ti ti-package"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Products</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_products']) }}</h3>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-gray-500">
                    <span class="text-green-500 font-semibold flex items-center">
                        <i class="ti ti-arrow-up-right mr-1"></i>
                    </span>
                    <span class="ml-1">In your inventory</span>
                </div>
            </div>

            <!-- Total Stock -->
            <div class="p-5 transition-all duration-300 bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-12 h-12 bg-emerald-100 rounded-xl text-emerald-600">
                        <i class="text-2xl ti ti-database"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Items</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_stock']) }}</h3>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-gray-500">
                    <span class="ml-1">Across {{ $stats['total_warehouses'] }} warehouses</span>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="p-5 transition-all duration-300 bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-12 h-12 bg-amber-100 rounded-xl text-amber-600">
                        <i class="text-2xl ti ti-alert-triangle"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Low Stock</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['low_stock_count']) }}</h3>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-gray-500">
                    @if($stats['low_stock_count'] > 0)
                        <a href="{{ route('inventory.low-stock') }}" class="text-amber-600 hover:underline font-medium">View items needing restock</a>
                    @else
                        <span class="text-emerald-500 font-medium">All levels healthy</span>
                    @endif
                </div>
            </div>

            <!-- Inventory Value -->
            <div class="p-5 transition-all duration-300 bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-xl text-indigo-600">
                        <i class="text-2xl ti ti-coin"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Value</p>
                        <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($stats['inventory_value'], 0, ',', '.') }}</h3>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-gray-500">
                    <span class="ml-1">Based on standard cost</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">
            <!-- Stock by Category Chart -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm lg:col-span-2 rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Stock Distribution by Category</h3>
                    <div class="p-2 bg-gray-50 rounded-lg">
                        <i class="ti ti-chart-pie text-gray-400"></i>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            <!-- Expiring Soon / Quick Actions -->
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Alerts & Actions</h3>
                <div class="space-y-4">
                    <!-- Expiring Soon -->
                    <div class="flex items-start p-3 bg-red-50 rounded-xl border border-red-100">
                        <div class="p-2 bg-red-100 rounded-lg text-red-600 mr-3">
                            <i class="ti ti-hourglass-empty"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-red-800">Expiring Soon</p>
                            <p class="text-xs text-red-600">{{ $stats['expiring_soon_count'] }} items expiring in 30 days</p>
                            <a href="{{ route('inventory.expiring') }}" class="inline-block mt-2 text-xs font-bold text-red-700 hover:underline track">Review Batches &rarr;</a>
                        </div>
                    </div>

                    <!-- Quick Transaction Actions -->
                    <div class="pt-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Quick Transactions</p>
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('stock-ins.create') }}" class="flex flex-col items-center p-3 text-center bg-gray-50 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-xl border border-gray-100">
                                <i class="ti ti-arrow-down-left text-xl mb-1"></i>
                                <span class="text-xs font-medium">Stock In</span>
                            </a>
                            <a href="{{ route('stock-outs.create') }}" class="flex flex-col items-center p-3 text-center bg-gray-50 hover:bg-orange-50 hover:text-orange-600 transition-colors rounded-xl border border-gray-100">
                                <i class="ti ti-arrow-up-right text-xl mb-1"></i>
                                <span class="text-xs font-medium">Stock Out</span>
                            </a>
                        </div>
                    </div>

                    <div class="pt-2">
                         <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                             <div class="flex items-center">
                                 <i class="ti ti-activity text-indigo-600 mr-2"></i>
                                 <span class="text-xs font-bold text-indigo-800 uppercase">Monthly Approval</span>
                             </div>
                             <span class="text-xs font-bold text-indigo-700">{{ $monthly_transactions['stock_in'] + $monthly_transactions['stock_out'] }} Total</span>
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Movements -->
        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Recent Stock Movements</h3>
                    <p class="text-xs text-gray-500">Last 5 activities across all warehouses</p>
                </div>
                <a href="{{ route('reports.movements') }}" class="text-sm font-bold text-blue-600 hover:text-blue-700">View All Report</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Product</th>
                            <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Warehouse</th>
                            <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Qty</th>
                            <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recent_movements as $movement)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 text-sm text-gray-600">{{ $movement->created_at->format('d M, H:i') }}</td>
                                <td class="py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $movement->product->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $movement->product->code }}</div>
                                </td>
                                <td class="py-4">
                                    @php
                                        $type = strtolower($movement->transaction_type);
                                        $typeColor = match($type) {
                                            'in', 'stock_in' => 'text-emerald-600 bg-emerald-50',
                                            'out', 'stock_out' => 'text-orange-600 bg-orange-50',
                                            'transfer', 'stock_transfer' => 'text-blue-600 bg-blue-50',
                                            'adjustment', 'stock_adjustment' => 'text-purple-600 bg-purple-50',
                                            default => 'text-gray-600 bg-gray-50'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColor }} uppercase">
                                        {{ str_replace('_', ' ', $type) }}
                                    </span>
                                </td>
                                <td class="py-4 text-sm text-gray-600">{{ $movement->warehouse->name }}</td>
                                <td class="py-4 font-bold text-sm {{ $movement->quantity > 0 ? 'text-emerald-600' : 'text-orange-600' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity) }}
                                </td>
                                <td class="py-4">
                                    <div class="flex items-center">
                                        <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-[10px] font-bold text-blue-600 mr-2">
                                            {{ substr($movement->user->name, 0, 2) }}
                                        </div>
                                        <span class="text-xs text-gray-600">{{ $movement->user->name }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400 text-sm italic">No recent movements found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            
            const categoryData = @json($stock_by_category);
            const labels = categoryData.map(item => item.name);
            const data = categoryData.map(item => item.total_quantity);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Stock',
                        data: data,
                        backgroundColor: '#5D87FF',
                        borderRadius: 8,
                        barThickness: 32,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    family: 'Plus Jakarta Sans',
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Plus Jakarta Sans',
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-modernize-layout>
