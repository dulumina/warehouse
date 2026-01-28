@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <a href="{{ route('categories.index') }}" class="btn btn-secondary mb-4">← Back</a>
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $category->name }}</h1>
            <p class="text-gray-500">{{ $category->description ?? 'No description' }}</p>
        </div>
    </div>
@endsection
