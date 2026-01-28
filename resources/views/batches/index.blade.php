@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Batches</h1>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Batch Number</th>
                        <th class="px-6 py-3 text-left">Product</th>
                        <th class="px-6 py-3 text-left">Expiry</th>
                        <th class="px-6 py-3 text-right">Qty</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($batches as $batch)
                        <tr>
                            <td class="px-6 py-4 font-mono">{{ $batch->batch_number }}</td>
                            <td class="px-6 py-4">{{ $batch->product?->name }}</td>
                            <td class="px-6 py-4">{{ $batch->expiry_date?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">{{ $batch->quantity }}</td>
                            <td class="px-6 py-4"><span class="badge">{{ $batch->status }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No batches</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
