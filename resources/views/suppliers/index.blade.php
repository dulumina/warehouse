@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Suppliers</h1>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td class="px-6 py-4">{{ $supplier->name }}</td>
                            <td class="px-6 py-4">{{ $supplier->email ?? '-' }}</td>
                            <td class="px-6 py-4 text-right"><a href="{{ route('suppliers.edit', $supplier) }}"
                                    class="btn btn-sm btn-warning">Edit</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No suppliers</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
