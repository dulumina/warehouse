@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="ti ti-plus mr-2"></i> Add Category
            </a>
        </div>

        <div class="bg-white rounded-lg shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Code</th>
                        <th class="px-6 py-3 text-left font-semibold">Name</th>
                        <th class="px-6 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $category->code }}</td>
                            <td class="px-6 py-4">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No categories</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
