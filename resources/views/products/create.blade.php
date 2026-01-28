@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Add Product</h1>

        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Code <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="code" name="code" value="{{ old('code') }}" required
                            class="form-control @error('code') is-invalid @enderror">
                        @error('code')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category <span
                                class="text-red-500">*</span></label>
                        <select id="category_id" name="category_id" required
                            class="form-control @error('category_id') is-invalid @enderror">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">Unit <span
                                class="text-red-500">*</span></label>
                        <select id="unit_id" name="unit_id" required
                            class="form-control @error('unit_id') is-invalid @enderror">
                            <option value="">Select Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type <span
                                class="text-red-500">*</span></label>
                        <select id="type" name="type" required
                            class="form-control @error('type') is-invalid @enderror">
                            <option value="raw" {{ old('type') == 'raw' ? 'selected' : '' }}>Raw</option>
                            <option value="finished" {{ old('type') == 'finished' ? 'selected' : '' }}>Finished</option>
                            <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service</option>
                        </select>
                    </div>

                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Cost <span
                                class="text-red-500">*</span></label>
                        <input type="number" id="cost" name="cost" step="0.01" value="{{ old('cost') }}"
                            required class="form-control @error('cost') is-invalid @enderror">
                        @error('cost')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price <span
                                class="text-red-500">*</span></label>
                        <input type="number" id="price" name="price" step="0.01" value="{{ old('price') }}"
                            required class="form-control @error('price') is-invalid @enderror">
                        @error('price')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label for="min_stock" class="block text-sm font-medium text-gray-700 mb-2">Min Stock <span
                                class="text-red-500">*</span></label>
                        <input type="number" id="min_stock" name="min_stock" step="0.01" value="{{ old('min_stock') }}"
                            required class="form-control @error('min_stock') is-invalid @enderror">
                    </div>

                    <div>
                        <label for="max_stock" class="block text-sm font-medium text-gray-700 mb-2">Max Stock <span
                                class="text-red-500">*</span></label>
                        <input type="number" id="max_stock" name="max_stock" step="0.01" value="{{ old('max_stock') }}"
                            required class="form-control @error('max_stock') is-invalid @enderror">
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight</label>
                        <input type="number" id="weight" name="weight" step="0.01" value="{{ old('weight') }}"
                            class="form-control @error('weight') is-invalid @enderror">
                    </div>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check mr-2"></i> Save Product
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="ti ti-x mr-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
