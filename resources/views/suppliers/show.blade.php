@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary mb-4">← Back</a>
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $supplier->name }}</h1>
            @if ($supplier->email)
                <p class="text-blue-600">{{ $supplier->email }}</p>
            @endif
        </div>
    </div>
@endsection
