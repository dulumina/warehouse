@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Add Category</h1>

        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Code <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="code" name="code" required class="form-control">
                </div>
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" required class="form-control">
                </div>
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
