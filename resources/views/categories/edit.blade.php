@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Category</h1>

        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Code <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code', $category->code) }}" required
                        class="form-control">
                </div>
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
                        class="form-control">
                </div>
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
