@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('stock-adjustments.index') }}" class="hover:text-blue-600">Stock Adjustment</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">{{ $adjustment->document_number }}</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Stock Adjustment Details</h2>
                <p class="text-sm text-gray-500 mt-1">View adjustment information and items</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('stock-adjustments.index') }}"
                    class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    <i class="ti ti-arrow-left mr-2"></i>
                    Back to List
                </a>
                
                @if ($adjustment->status === 'DRAFT')
                    <form action="{{ route('stock-adjustments.approve', $adjustment) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Approve this adjustment? This will update inventory levels.')"
                            class="inline-flex items-center px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                            <i class="ti ti-check mr-2"></i>
                            Approve
                        </button>
                    </form>

                    <form action="{{ route('stock-adjustments.reject', $adjustment) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Reject this adjustment?')"
                            class="inline-flex items-center px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all">
                            <i class="ti ti-x mr-2"></i>
                            Reject
                        </button>
                    </form>
                @endif

                @if ($adjustment->status === 'DRAFT')
                    <form action="{{ route('stock-adjustments.destroy', $adjustment) }}" method="POST" class="inline"
                        onsubmit="return confirm('Delete this adjustment?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                            <i class="ti ti-trash mr-2"></i>
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Adjustment Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="ti ti-file-info mr-2 text-blue-600"></i>
                            Adjustment Information
                        </h3>
                        @php
                            $statusClasses = [
                                'DRAFT' => 'bg-gray-100 text-gray-800',
                                'APPROVED' => 'bg-green-100 text-green-800',
                                'REJECTED' => 'bg-red-100 text-red-800',
                            ];
                            $statusClass = $statusClasses[$adjustment->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                            {{ $adjustment->status }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Document Number</label>
                            <p class="text-sm font-mono font-semibold text-blue-600">{{ $adjustment->document_number }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Adjustment Date</label>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $adjustment->adjustment_date?->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Warehouse</label>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $adjustment->warehouse?->name ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $adjustment->warehouse?->code }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                            @php
                                $typeLabels = [
                                    'PHYSICAL_COUNT' => 'Physical Count',
                                    'CORRECTION' => 'Correction',
                                    'DAMAGED' => 'Damaged Goods',
                                    'EXPIRED' => 'Expired Items',
                                    'FOUND' => 'Found Items',
                                ];
                                $typeLabel = $typeLabels[$adjustment->type] ?? $adjustment->type;
                            @endphp
                            <p class="text-sm font-medium text-gray-900">{{ $typeLabel }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Adjusted By</label>
                            <p class="text-sm font-medium text-gray-900">{{ $adjustment->adjustedBy?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $adjustment->created_at?->format('d M Y H:i') }}</p>
                        </div>
                        @if($adjustment->approved_by)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">
                                {{ $adjustment->status === 'APPROVED' ? 'Approved' : 'Rejected' }} By
                            </label>
                            <p class="text-sm font-medium text-gray-900">{{ $adjustment->approvedBy?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $adjustment->approved_at?->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                        @if($adjustment->notes)
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Notes</label>
                            <p class="text-sm text-gray-900">{{ $adjustment->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Adjustment Items -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="ti ti-adjustments-horizontal mr-2 text-blue-600"></i>
                        Adjustment Items
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Product
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Location
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    System Qty
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actual Qty
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Difference
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Value Impact
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($adjustment->items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="ti ti-box text-blue-600 text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $item->product?->name }}</p>
                                                <p class="text-xs text-gray-500 font-mono">{{ $item->product?->code }}</p>
                                                @if($item->batch_number)
                                                    <p class="text-xs text-gray-500">Batch: {{ $item->batch_number }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-900">{{ $item->location?->name ?? '-' }}</p>
                                        @if($item->location)
                                            <p class="text-xs text-gray-500">{{ $item->location->code }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-sm text-gray-900">
                                            {{ number_format($item->system_quantity, 2) }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $item->product?->unit?->symbol }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ number_format($item->actual_quantity, 2) }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $item->product?->unit?->symbol }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @php
                                            $diffClass = $item->difference > 0 ? 'text-green-600' : ($item->difference < 0 ? 'text-red-600' : 'text-gray-900');
                                            $diffIcon = $item->difference > 0 ? 'ti-arrow-up' : ($item->difference < 0 ? 'ti-arrow-down' : 'ti-minus');
                                        @endphp
                                        <p class="text-sm font-bold {{ $diffClass }} flex items-center justify-end">
                                            <i class="ti {{ $diffIcon }} mr-1"></i>
                                            {{ number_format(abs($item->difference), 2) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @php
                                            $valueClass = $item->value_difference > 0 ? 'text-green-600' : ($item->value_difference < 0 ? 'text-red-600' : 'text-gray-900');
                                        @endphp
                                        <p class="text-sm font-bold {{ $valueClass }}">
                                            {{ $item->value_difference >= 0 ? '+' : '' }}Rp {{ number_format(abs($item->value_difference), 2) }}
                                        </p>
                                    </td>
                                </tr>
                                @if($item->reason)
                                    <tr class="bg-gray-50">
                                        <td colspan="6" class="px-6 py-2">
                                            <p class="text-xs text-gray-600">
                                                <i class="ti ti-note text-gray-400 mr-1"></i>
                                                <span class="font-medium">Reason:</span> {{ $item->reason }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <i class="ti ti-adjustments-off text-6xl text-gray-300 mb-2"></i>
                                        <p class="text-gray-500">No items found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($adjustment->items->count() > 0)
                        <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-semibold text-gray-900">
                                    Total Impact:
                                </td>
                                <td colspan="2" class="px-6 py-4 text-right">
                                    @php
                                        $totalImpact = $adjustment->items->sum('value_difference');
                                        $impactClass = $totalImpact > 0 ? 'text-green-600' : ($totalImpact < 0 ? 'text-red-600' : 'text-gray-900');
                                    @endphp
                                    <p class="text-lg font-bold {{ $impactClass }}">
                                        {{ $totalImpact >= 0 ? '+' : '' }}Rp {{ number_format(abs($totalImpact), 2) }}
                                    </p>
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="ti ti-clock mr-2 text-blue-600"></i>
                        Adjustment Timeline
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Created -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="ti ti-file-plus text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Adjustment Created</p>
                                <p class="text-xs text-gray-500">
                                    by {{ $adjustment->adjustedBy?->name }} • {{ $adjustment->created_at?->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>

                        <!-- Approved/Rejected -->
                        @if($adjustment->approved_at)
                        <div class="flex items-start">
                            @if($adjustment->status === 'APPROVED')
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="ti ti-check text-green-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Adjustment Approved</p>
                                    <p class="text-xs text-gray-500">
                                        by {{ $adjustment->approvedBy?->name }} • {{ $adjustment->approved_at?->format('d M Y H:i') }}
                                    </p>
                                </div>
                            @else
                                <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="ti ti-x text-red-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Adjustment Rejected</p>
                                    <p class="text-xs text-gray-500">
                                        by {{ $adjustment->approvedBy?->name }} • {{ $adjustment->approved_at?->format('d M Y H:i') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Summary Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="ti ti-report-analytics mr-2 text-blue-600"></i>
                        Summary
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Total Items</span>
                        <span class="text-lg font-bold text-gray-900">{{ $adjustment->items->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Positive Adj.</span>
                        <span class="text-lg font-bold text-green-600">
                            {{ $adjustment->items->where('difference', '>', 0)->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Negative Adj.</span>
                        <span class="text-lg font-bold text-red-600">
                            {{ $adjustment->items->where('difference', '<', 0)->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Value Impact</span>
                        @php
                            $totalImpact = $adjustment->items->sum('value_difference');
                            $impactClass = $totalImpact > 0 ? 'text-green-600' : ($totalImpact < 0 ? 'text-red-600' : 'text-blue-600');
                        @endphp
                        <span class="text-lg font-bold {{ $impactClass }}">
                            {{ $totalImpact >= 0 ? '+' : '' }}Rp {{ number_format(abs($totalImpact), 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Type Info -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="ti ti-info-circle text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Adjustment Type</h3>
                        <p class="text-sm text-blue-800 font-medium mb-1">{{ $typeLabel }}</p>
                        <p class="text-xs text-blue-700">
                            @switch($adjustment->type)
                                @case('PHYSICAL_COUNT')
                                    Regular stock counting reconciliation to match physical inventory with system records.
                                    @break
                                @case('CORRECTION')
                                    Correction of data entry errors or system discrepancies.
                                    @break
                                @case('DAMAGED')
                                    Removal of damaged or defective inventory items.
                                    @break
                                @case('EXPIRED')
                                    Removal of expired or obsolete inventory items.
                                    @break
                                @case('FOUND')
                                    Addition of discovered inventory not previously recorded in the system.
                                    @break
                            @endswitch
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            @if($adjustment->status === 'DRAFT')
            <div class="bg-amber-50 rounded-xl p-6 border border-amber-100">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="ti ti-alert-circle text-2xl text-amber-600"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-amber-900 mb-2">Pending Approval</h3>
                        <div class="text-xs text-amber-800 space-y-1">
                            <p>• Review all adjustment items carefully</p>
                            <p>• Verify quantities and reasons</p>
                            <p>• Check value impact on inventory</p>
                            <p>• Approve to update inventory levels</p>
                            <p>• Reject if adjustments are incorrect</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($adjustment->status === 'APPROVED')
            <div class="bg-green-50 rounded-xl p-6 border border-green-100">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="ti ti-circle-check text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-green-900 mb-2">Adjustment Approved</h3>
                        <p class="text-xs text-green-800">This adjustment has been approved and inventory levels have been updated accordingly.</p>
                    </div>
                </div>
            </div>
            @endif

            @if($adjustment->status === 'REJECTED')
            <div class="bg-red-50 rounded-xl p-6 border border-red-100">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="ti ti-circle-x text-2xl text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-red-900 mb-2">Adjustment Rejected</h3>
                        <p class="text-xs text-red-800">This adjustment has been rejected and no inventory changes have been made.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection