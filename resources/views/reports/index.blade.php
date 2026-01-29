@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">Reports</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Reports Center</h2>
                <p class="text-sm text-gray-500 mt-1">Access detailed analytics and inventory history</p>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Stock Report Card -->
        <a href="{{ route('reports.stock') }}" class="group block bg-white rounded-2xl shadow-sm border border-gray-200 p-8 hover:shadow-xl hover:border-blue-500 transition-all duration-300">
            <div class="bg-blue-50 rounded-xl p-4 w-16 h-16 flex items-center justify-center mb-6 group-hover:bg-blue-500 transition-colors">
                <i class="ti ti-package text-blue-600 text-3xl group-hover:text-white"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Stock Report</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                View current inventory levels, low stock alerts, and item placement across all warehouses.
            </p>
            <div class="flex items-center text-blue-600 font-semibold text-sm">
                Open Report <i class="ti ti-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

        <!-- Movement Report Card -->
        <a href="{{ route('reports.movements') }}" class="group block bg-white rounded-2xl shadow-sm border border-gray-200 p-8 hover:shadow-xl hover:border-green-500 transition-all duration-300">
            <div class="bg-green-50 rounded-xl p-4 w-16 h-16 flex items-center justify-center mb-6 group-hover:bg-green-500 transition-colors">
                <i class="ti ti-arrows-exchange text-green-600 text-3xl group-hover:text-white"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Stock Movements</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                Monitor the history of all transactions including Stock In, Out, Transfers, and Adjustments.
            </p>
            <div class="flex items-center text-green-600 font-semibold text-sm">
                Open Report <i class="ti ti-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

        <!-- Valuation Report Card -->
        <a href="{{ route('reports.valuation') }}" class="group block bg-white rounded-2xl shadow-sm border border-gray-200 p-8 hover:shadow-xl hover:border-purple-500 transition-all duration-300">
            <div class="bg-purple-50 rounded-xl p-4 w-16 h-16 flex items-center justify-center mb-6 group-hover:bg-purple-500 transition-colors">
                <i class="ti ti-currency-dollar text-purple-600 text-3xl group-hover:text-white"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Inventory Valuation</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                Analyze the financial value of your assets with detailed cost breakdown per warehouse.
            </p>
            <div class="flex items-center text-purple-600 font-semibold text-sm">
                Open Report <i class="ti ti-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>
    </div>

    <!-- Quick Insights -->
    <div class="mt-12">
        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
            <i class="ti ti-bulb mr-2 text-yellow-500"></i> Reporting Tips
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex-shrink-0">
                    <div class="bg-blue-100 text-blue-600 rounded-lg p-2">
                        <i class="ti ti-search"></i>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm">Real-time Search</h4>
                    <p class="text-gray-500 text-xs mt-1">All reports support real-time searching. Just type in the search box to filter results instantly.</p>
                </div>
            </div>
            <div class="flex gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex-shrink-0">
                    <div class="bg-indigo-100 text-indigo-600 rounded-lg p-2">
                        <i class="ti ti-adjustments"></i>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm">Dynamic Summaries</h4>
                    <p class="text-gray-500 text-xs mt-1">Summary cards at the top of each report update automatically based on your search filters.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
