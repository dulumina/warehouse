@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('stock-ins.index') }}" class="hover:text-blue-600">Stock In</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">{{ $stockIn->document_number }}</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Stock In Details</h2>
                <p class="text-sm text-gray-500 mt-1">View stock in transaction information</p>
            </div>
            <div class="flex gap-2">
                @if ($stockIn->status === 'DRAFT')
                    <form action="{{ route('stock-ins.pending', $stockIn) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <i class="ti ti-send mr-2"></i>
                            Submit for Approval
                        </button>
                    </form>
                @endif

                @if ($stockIn->status === 'PENDING' && auth()->user()->can('approve stock in'))
                    <form action="{{ route('stock-ins.approve', $stockIn) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all"
                            onclick="return confirm('Are you sure you want to approve this stock in?')">
                            <i class="ti ti-check mr-2"></i>
                            Approve
                        </button>
                    </form>
                    <form action="{{ route('stock-ins.reject', $stockIn) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all"
                            onclick="return confirm('Are you sure you want to reject this stock in?')">
                            <i class="ti ti-x mr-2"></i>
                            Reject
                        </button>
                    </form>
                @endif

                <a href="{{ route('stock-ins.index') }}"
                    class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    <i class="ti ti-arrow-left mr-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- General Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="ti ti-file-info mr-2 text-blue-600"></i>
                        General Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Document Number</label>
                            <p class="text-lg font-bold text-blue-600 font-mono">{{ $stockIn->document_number }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Status</label>
                            <div class="mt-1">
                                @php
                                    $badgeClass = match ($stockIn->status) {
                                        'APPROVED' => 'badge-success',
                                        'REJECTED' => 'badge-error',
                                        'PENDING' => 'badge-warning',
                                        default => 'badge-ghost',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} gap-2">
                                    @if ($stockIn->status === 'APPROVED')
                                        <i class="ti ti-check"></i>
                                    @elseif($stockIn->status === 'REJECTED')
                                        <i class="ti ti-x"></i>
                                    @elseif($stockIn->status === 'PENDING')
                                        <i class="ti ti-clock"></i>
                                    @else
                                        <i class="ti ti-file"></i>
                                    @endif
                                    {{ $stockIn->status }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Warehouse</label>
                            <p class="text-base font-semibold text-gray-900">{{ $stockIn->warehouse?->name ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $stockIn->warehouse?->city ?? '' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Transaction Date</label>
                            <p class="text-base font-semibold text-gray-900">
                                {{ $stockIn->transaction_date?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Supplier</label>
                            <p class="text-base font-semibold text-gray-900">{{ $stockIn->supplier?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Type</label>
                            <p class="text-base font-semibold text-gray-900">
                                {{ str_replace('_', ' ', ucfirst(strtolower($stockIn->type))) }}</p>
                        </div>
                        @if ($stockIn->reference_number)
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Reference
                                    Number</label>
                                <p class="text-base font-semibold text-gray-900 font-mono">{{ $stockIn->reference_number }}
                                </p>
                            </div>
                        @endif
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Received By</label>
                            <p class="text-base font-semibold text-gray-900">{{ $stockIn->receivedBy?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Created At</label>
                            <p class="text-base font-semibold text-gray-900">
                                {{ $stockIn->created_at?->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                        @if ($stockIn->approved_by)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Approved By</label>
                                <p class="text-base font-semibold text-gray-900">{{ $stockIn->approvedBy?->name ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Approved At</label>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ $stockIn->approved_at?->format('d M Y H:i') ?? '-' }}</p>
                            </div>
                        @endif
                        @if ($stockIn->notes)
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Notes</label>
                                <div class="mt-1 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="text-sm text-gray-700">{{ $stockIn->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="ti ti-package mr-2 text-blue-600"></i>
                        Items ({{ $stockIn->items?->count() ?? 0 }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    @if ($stockIn->items && $stockIn->items->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Product
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Batch Number
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Unit Cost
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($stockIn->items as $index => $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $item->product?->name ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->product?->code ?? '' }}</div>
                                            @if ($item->notes)
                                                <div class="text-xs text-blue-600 mt-1">
                                                    <i class="ti ti-note text-xs"></i> {{ $item->notes }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($item->batch_number)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $item->batch_number }}
                                                </span>
                                                @if ($item->expiry_date)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Exp: {{ $item->expiry_date->format('d M Y') }}
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-sm text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                            {{ number_format($item->quantity, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                            ${{ number_format($item->unit_cost, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                            ${{ number_format($item->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-900">
                                        Total:
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                        {{ number_format($stockIn->total_quantity, 2) }}
                                    </td>
                                    <td class="px-6 py-4"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-lg font-bold text-blue-600">
                                        ${{ number_format($stockIn->total_value, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ti ti-package-off text-3xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No items found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6 lg:sticky lg:top-6 self-start">
            <!-- Summary Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="ti ti-receipt mr-2 text-blue-600"></i>
                        Summary
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Total Items</span>
                        <span class="text-lg font-bold text-gray-900">{{ $stockIn->total_items ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Total Quantity</span>
                        <span
                            class="text-lg font-bold text-gray-900">{{ number_format($stockIn->total_quantity ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-base font-semibold text-gray-900">Total Value</span>
                        <span
                            class="text-xl font-bold text-blue-600">${{ number_format($stockIn->total_value ?? 0, 2) }}</span>
                    </div>
                </div>

                @if ($stockIn->status === 'DRAFT')
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                        <form action="{{ route('stock-ins.destroy', $stockIn) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this stock in?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-6 py-3 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all">
                                <i class="ti ti-trash mr-2"></i>
                                Delete Draft
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 max-h-[90vh] h-[40vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="ti ti-timeline mr-2 text-blue-600"></i>
                        Activity Timeline
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
                                        aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span
                                                class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="ti ti-plus text-white text-sm"></i>
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm text-gray-900 font-medium">Created</p>
                                                <p class="text-xs text-gray-500">{{ $stockIn->receivedBy?->name ?? '-' }}
                                                </p>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-xs text-gray-500">
                                                <p>{{ $stockIn->created_at?->format('d M Y') }}</p>
                                                <p>{{ $stockIn->created_at?->format('H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            @if ($stockIn->status === 'PENDING' || $stockIn->status === 'APPROVED' || $stockIn->status === 'REJECTED')
                                <li>
                                    <div class="relative pb-8">
                                        @if ($stockIn->status === 'APPROVED' || $stockIn->status === 'REJECTED')
                                            <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
                                                aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span
                                                    class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="ti ti-clock text-white text-sm"></i>
                                                </span>
                                            </div>
                                            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                <div>
                                                    <p class="text-sm text-gray-900 font-medium">Submitted for Approval</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @if ($stockIn->status === 'APPROVED')
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span
                                                    class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="ti ti-check text-white text-sm"></i>
                                                </span>
                                            </div>
                                            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                <div>
                                                    <p class="text-sm text-gray-900 font-medium">Approved</p>
                                                    <p class="text-xs text-gray-500">{{ $stockIn->approvedBy?->name ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="whitespace-nowrap text-right text-xs text-gray-500">
                                                    <p>{{ $stockIn->approved_at?->format('d M Y') }}</p>
                                                    <p>{{ $stockIn->approved_at?->format('H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @elseif($stockIn->status === 'REJECTED')
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span
                                                    class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="ti ti-x text-white text-sm"></i>
                                                </span>
                                            </div>
                                            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                <div>
                                                    <p class="text-sm text-gray-900 font-medium">Rejected</p>
                                                    <p class="text-xs text-gray-500">{{ $stockIn->approvedBy?->name ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="whitespace-nowrap text-right text-xs text-gray-500">
                                                    <p>{{ $stockIn->approved_at?->format('d M Y') }}</p>
                                                    <p>{{ $stockIn->approved_at?->format('H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection