@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('stock-outs.index') }}" class="hover:text-blue-600">Stock Out</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">{{ $stockOut->document_number }}</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Stock Out Details</h2>
                <p class="text-sm text-gray-500 mt-1">View stock out transaction information</p>
            </div>
            <div class="flex gap-2">
                @if ($stockOut->status === 'draft')
                    <form action="{{ route('stock-outs.pending', $stockOut) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <i class="ti ti-send mr-2"></i>
                            Submit for Approval
                        </button>
                    </form>
                @endif

                @if ($stockOut->status === 'pending' && auth()->user()->can('update', $stockOut))
                    <form action="{{ route('stock-outs.approve', $stockOut) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all"
                            onclick="return confirm('Are you sure you want to approve this stock out?')">
                            <i class="ti ti-check mr-2"></i>
                            Approve
                        </button>
                    </form>
                    <form action="{{ route('stock-outs.reject', $stockOut) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all"
                            onclick="return confirm('Are you sure you want to reject this stock out?')">
                            <i class="ti ti-x mr-2"></i>
                            Reject
                        </button>
                    </form>
                @endif

                <a href="{{ route('stock-outs.index') }}"
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
                            <p class="text-lg font-bold text-blue-600 font-mono">{{ $stockOut->document_number }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Status</label>
                            <div class="mt-1">
                                @php
                                    $badgeClass = match ($stockOut->status) {
                                        'approved' => 'badge-success',
                                        'rejected' => 'badge-error',
                                        'pending' => 'badge-warning',
                                        default => 'badge-ghost',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} gap-2">
                                    @if ($stockOut->status === 'approved')
                                        <i class="ti ti-check"></i>
                                    @elseif($stockOut->status === 'rejected')
                                        <i class="ti ti-x"></i>
                                    @elseif($stockOut->status === 'pending')
                                        <i class="ti ti-clock"></i>
                                    @else
                                        <i class="ti ti-file"></i>
                                    @endif
                                    {{ ucfirst($stockOut->status) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Warehouse</label>
                            <p class="text-base font-semibold text-gray-900">{{ $stockOut->warehouse?->name ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $stockOut->warehouse?->city ?? '' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Transaction Date</label>
                            <p class="text-base font-semibold text-gray-900">
                                {{ $stockOut->transaction_date?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Type</label>
                            <p class="text-base font-semibold text-gray-900">{{ ucfirst($stockOut->type) }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Created By</label>
                            <p class="text-base font-semibold text-gray-900">{{ $stockOut->user?->name ?? '-' }}</p>
                        </div>
                        @if ($stockOut->notes)
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Notes</label>
                                <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $stockOut->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="ti ti-package mr-2 text-blue-600"></i>
                        Items
                    </h3>
                </div>
                <div class="overflow-x-auto">
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
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantity
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reason
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Notes
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stockOut->items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product?->name ?? '-' }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $item->product?->sku ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-semibold">
                                        {{ number_format($item->quantity, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($item->reason)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($item->reason) }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $item->notes ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <i class="ti ti-package-off text-4xl mb-2 opacity-20"></i>
                                        <p class="text-sm">No items found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($stockOut->items->count() > 0)
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-sm font-semibold text-gray-900">
                                        Total
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                        {{ number_format($stockOut->items->sum('quantity'), 2) }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Summary -->
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
                        <span class="text-lg font-bold text-gray-900">{{ $stockOut->items->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Total Quantity</span>
                        <span class="text-lg font-bold text-gray-900">{{ number_format($stockOut->items->sum('quantity'), 2) }}</span>
                    </div>
                    <div class="pt-2">
                        <div class="flex items-center text-xs text-gray-500">
                            <i class="ti ti-calendar mr-1"></i>
                            Created {{ $stockOut->created_at?->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="ti ti-settings mr-2 text-blue-600"></i>
                        Actions
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('stock-outs.index') }}"
                        class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all">
                        <i class="ti ti-list mr-2"></i>
                        View All Stock Outs
                    </a>

                    @if ($stockOut->status === 'draft')
                        <form action="{{ route('stock-outs.destroy', $stockOut) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this draft?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-all">
                                <i class="ti ti-trash mr-2"></i>
                                Delete Draft
                            </button>
                        </form>
                    @endif
                </div>
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
                                                <p class="text-xs text-gray-500">{{ $stockOut->user?->name ?? '-' }}
                                                </p>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-xs text-gray-500">
                                                <p>{{ $stockOut->created_at?->format('d M Y') }}</p>
                                                <p>{{ $stockOut->created_at?->format('H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            @if ($stockOut->status === 'pending' || $stockOut->status === 'approved' || $stockOut->status === 'rejected')
                                <li>
                                    <div class="relative pb-8">
                                        @if ($stockOut->status === 'approved' || $stockOut->status === 'rejected')
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

                            @if ($stockOut->status === 'approved')
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
                                                    <p class="text-xs text-gray-500">{{ $stockOut->approvedBy?->name ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="whitespace-nowrap text-right text-xs text-gray-500">
                                                    <p>{{ $stockOut->approved_at?->format('d M Y') }}</p>
                                                    <p>{{ $stockOut->approved_at?->format('H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @elseif($stockOut->status === 'rejected')
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
                                                    <p class="text-xs text-gray-500">{{ $stockOut->rejectedBy?->name ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="whitespace-nowrap text-right text-xs text-gray-500">
                                                    <p>{{ $stockOut->rejected_at?->format('d M Y') }}</p>
                                                    <p>{{ $stockOut->rejected_at?->format('H:i') }}</p>
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