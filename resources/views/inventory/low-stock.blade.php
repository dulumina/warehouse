@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Low Stock Items</h1>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Product</th>
                        <th class="px-6 py-3 text-left">Warehouse</th>
                        <th class="px-6 py-3 text-right">Current</th>
                        <th class="px-6 py-3 text-right">Min Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($lowStock as $inv)
                        <tr class="bg-yellow-50">
                            <td class="px-6 py-4">{{ $inv->product?->name }}</td>
                            <td class="px-6 py-4">{{ $inv->warehouse?->name }}</td>
                            <td class="px-6 py-4 text-right">{{ $inv->quantity }}</td>
                            <td class="px-6 py-4 text-right">{{ $inv->product?->min_stock }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No low stock items</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
