@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Expiring Items</h1>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Product</th>
                        <th class="px-6 py-3 text-left">Batch</th>
                        <th class="px-6 py-3 text-left">Expiry Date</th>
                        <th class="px-6 py-3 text-right">Qty</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($expiringItems as $item)
                        <tr class="bg-red-50">
                            <td class="px-6 py-4">{{ $item->product?->name }}</td>
                            <td class="px-6 py-4">{{ $item->batch_number }}</td>
                            <td class="px-6 py-4">{{ $item->expiry_date?->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-right">{{ $item->quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No expiring items</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
