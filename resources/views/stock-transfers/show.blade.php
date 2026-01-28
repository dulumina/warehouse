@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <a href="{{ route('stock-transfers.index') }}" class="btn btn-secondary mb-4">← Back</a>
        <div class="bg-white rounded-lg shadow p-6">Stock Transfer Details</div>
    </div>
@endsection
