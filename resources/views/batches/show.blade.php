@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <a href="{{ route('batches.index') }}" class="btn btn-secondary mb-4">← Back</a>
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $batch->batch_number }}</h1>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-500 text-sm">Product</p>
                    <p class="font-semibold">{{ $batch->product?->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Quantity</p>
                    <p class="font-semibold">{{ $batch->quantity }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Expiry Date</p>
                    <p class="font-semibold">{{ $batch->expiry_date?->format('Y-m-d') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Status</p>
                    <p class="font-semibold"><span class="badge">{{ $batch->status }}</span></p>
                </div>
            </div>
        </div>
    </div>
@endsection
