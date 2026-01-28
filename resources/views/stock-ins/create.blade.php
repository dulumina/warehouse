@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Create Stock In</h1>
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form action="{{ route('stock-ins.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Warehouse <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" required class="form-control">
                        <option value="">Select Warehouse</option>
                        @foreach ($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="transaction_date" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@endsection
