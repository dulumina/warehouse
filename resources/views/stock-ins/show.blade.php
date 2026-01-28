@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <a href="{{ route('stock-ins.index') }}" class="btn btn-secondary mb-4">← Back</a>
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold mb-4">{{ $stockIn->document_number ?? 'Draft' }}</h1>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-500 text-sm">Warehouse</p>
                    <p class="font-semibold">{{ $stockIn->warehouse?->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Status</p>
                    <p class="font-semibold"><span class="badge">{{ $stockIn->status }}</span></p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Date</p>
                    <p class="font-semibold">{{ $stockIn->transaction_date?->format('Y-m-d') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Items</p>
                    <p class="font-semibold">{{ $stockIn->items?->count() ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
