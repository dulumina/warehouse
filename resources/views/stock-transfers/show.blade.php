@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('stock-transfers.index') }}" class="hover:text-blue-600">Stock Transfer</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">{{ $transfer->document_number }}</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Stock Transfer Details</h2>
                <p class="text-sm text-gray-500 mt-1">View transfer information and items</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('stock-transfers.index') }}"
                    class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    <i class="ti ti-arrow-left mr-2"></i>
                    Back to List
                </a>
                
                @if ($transfer->status === 'DRAFT')
                    <form action="{{ route('stock-transfers.send', $transfer) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Send this transfer? This will mark it as in transit.')"
                            class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <i class="ti ti-send mr-2"></i>
                            Send Transfer
                        </button>
                    </form>
                @endif

                @if ($transfer->status === 'IN_TRANSIT')
                    <form action="{{ route('stock-transfers.receive', $transfer) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Mark as received? This will update inventory levels.')"
                            class="inline-flex items-center px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                            <i class="ti ti-check mr-2"></i>
                            Receive Transfer
                        </button>
                    </form>
                @endif

                @if ($transfer->status === 'DRAFT')
                    <form action="{{ route('stock-transfers.destroy', $transfer) }}" method="POST" class="inline"
                        onsubmit="return confirm('Delete this transfer?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all">
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
            <!-- Transfer Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="ti ti-file-info mr-2 text-blue-600"></i>
                            Transfer Information
                        </h3>
                        @php
                            $statusClasses = [
                                'DRAFT' => 'bg-gray-100 text-gray-800',
                                'IN_TRANSIT' => 'bg-blue-100 text-blue-800',
                                'RECEIVED' => 'bg-green-100 text-green-800',
                                'REJECTED' => 'bg-red-100 text-red-800',
                                'CANCELLED' => 'bg-gray-100 text-gray-600',
                            ];
                            $statusClass = $statusClasses[$transfer->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                            {{ $transfer->status }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Document Number</label>
                            <p class="text-sm font-mono font-semibold text-blue-600">{{ $transfer->document_number }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Transfer Date</label>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $transfer->transaction_date?->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">From Warehouse</label>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $transfer->fromWarehouse?->name ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $transfer->fromWarehouse?->code }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">To Warehouse</label>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $transfer->toWarehouse?->name ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $transfer->toWarehouse?->code }}</p>
                        </div>
                        @if($transfer->sent_by)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Sent By</label>
                            <p class="text-sm font-medium text-gray-900">{{ $transfer->sentBy?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $transfer->sent_at?->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                        @if($transfer->received_by)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Received By</label>
                            <p class="text-sm font-medium text-gray-900">{{ $transfer->receivedBy?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $transfer->received_at?->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                        @if($transfer->notes)
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Notes</label>
                            <p class="text-sm text-gray-900">{{ $transfer->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Transfer Items -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="ti ti-package mr-2 text-blue-600"></i>
                        Transfer Items
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
                                    From Location
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    To Location
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Batch
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Qty Sent
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Qty Received
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($transfer->items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="ti ti-box text-blue-600 text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $item->product?->name }}</p>
                                                <p class="text-xs text-gray-500 font-mono">{{ $item->product?->code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-900">{{ $item->fromLocation?->name ?? '-' }}</p>
                                        @if($item->fromLocation)
                                            <p class="text-xs text-gray-500">{{ $item->fromLocation->code }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-900">{{ $item->toLocation?->name ?? '-' }}</p>
                                        @if($item->toLocation)
                                            <p class="text-xs text-gray-500">{{ $item->toLocation->code }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-mono text-gray-900">{{ $item->batch_number ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ number_format($item->quantity, 2) }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $item->product?->unit?->symbol }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($item->quantity_received)
                                            <p class="text-sm font-semibold text-green-600">
                                                {{ number_format($item->quantity_received, 2) }}
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-400">-</p>
                                        @endif
                                    </td>
                                </tr>
                                @if($item->notes)
                                    <tr class="bg-gray-50">
                                        <td colspan="6" class="px-6 py-2">
                                            <p class="text-xs text-gray-600">
                                                <i class="ti ti-note text-gray-400 mr-1"></i>
                                                {{ $item->notes }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <i class="ti ti-package-off text-6xl text-gray-300 mb-2"></i>
                                        <p class="text-gray-500">No items found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Timeline -->
            @if($transfer->status !== 'DRAFT')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="ti ti-clock mr-2 text-blue-600"></i>
                        Transfer Timeline
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
                                <p class="text-sm font-medium text-gray-900">Transfer Created</p>
                                <p class="text-xs text-gray-500">{{ $transfer->created_at?->format('d M Y H:i') }}</p>
                            </div>
                        </div>

                        <!-- Sent -->
                        @if($transfer->sent_at)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="ti ti-send text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Transfer Sent</p>
                                <p class="text-xs text-gray-500">
                                    by {{ $transfer->sentBy?->name }} • {{ $transfer->sent_at?->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>
                        @endif

                        <!-- Received -->
                        @if($transfer->received_at)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="ti ti-check text-green-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Transfer Received</p>
                                <p class="text-xs text-gray-500">
                                    by {{ $transfer->receivedBy?->name }} • {{ $transfer->received_at?->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
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
                        <span class="text-lg font-bold text-gray-900">{{ $transfer->total_items ?? $transfer->items->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Quantity</span>
                        <span class="text-lg font-bold text-blue-600">
                            {{ number_format($transfer->total_quantity ?? $transfer->items->sum('quantity'), 2) }}
                        </span>
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
                            <div class="bg-white/60 rounded-lg p-3">
                                <p class="text-xs text-blue-700 mb-1">From</p>
                                <p class="text-sm font-semibold text-blue-900">{{ $transfer->fromWarehouse?->name }}</p>
                                <p class="text-xs text-blue-600">{{ $transfer->fromWarehouse?->city }}</p>
                            </div>
                            <div class="flex items-center justify-center">
                                <i class="ti ti-arrow-down text-blue-600 text-xl"></i>
                            </div>
                            <div class="bg-white/60 rounded-lg p-3">
                                <p class="text-xs text-blue-700 mb-1">To</p>
                                <p class="text-sm font-semibold text-blue-900">{{ $transfer->toWarehouse?->name }}</p>
                                <p class="text-xs text-blue-600">{{ $transfer->toWarehouse?->city }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            @if($transfer->status === 'DRAFT' || $transfer->status === 'IN_TRANSIT')
            <div class="bg-amber-50 rounded-xl p-6 border border-amber-100">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="ti ti-alert-circle text-2xl text-amber-600"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-amber-900 mb-2">Next Actions</h3>
                        <div class="text-xs text-amber-800 space-y-1">
                            @if($transfer->status === 'DRAFT')
                                <p>• Review transfer details</p>
                                <p>• Verify all items and quantities</p>
                                <p>• Click "Send Transfer" when ready</p>
                            @elseif($transfer->status === 'IN_TRANSIT')
                                <p>• Verify items upon arrival</p>
                                <p>• Check quantities received</p>
                                <p>• Click "Receive Transfer" to complete</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($transfer->status === 'RECEIVED')
            <div class="bg-green-50 rounded-xl p-6 border border-green-100">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="ti ti-circle-check text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-green-900 mb-2">Transfer Complete</h3>
                        <p class="text-xs text-green-800">This transfer has been successfully received and inventory has been updated.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection