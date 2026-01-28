@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Stock In</h1>
            <a href="{{ route('stock-ins.create') }}" class="btn btn-primary">+ Create</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Document #</th>
                        <th class="px-6 py-3 text-left">Warehouse</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($stockIns as $item)
                        <tr>
                            <td class="px-6 py-4 font-mono">{{ $item->document_number }}</td>
                            <td class="px-6 py-4">{{ $item->warehouse?->name }}</td>
                            <td class="px-6 py-4"><span class="badge badge-{{ $item->status }}">{{ $item->status }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $item->transaction_date?->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-right"><a href="{{ route('stock-ins.show', $item) }}"
                                    class="btn btn-sm btn-info">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No stock in records</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
