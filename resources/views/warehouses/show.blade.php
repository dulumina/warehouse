@extends('layouts.modernize')

@section('content')
    <div class="container px-6 py-6 mx-auto max-w-7xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $warehouse->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $warehouse->code }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-edit"></i> Edit
                </a>
                <a href="{{ route('warehouses.index') }}" class="btn btn-ghost btn-sm">
                    <i class="ti ti-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Info Cards Row -->
        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
            <!-- Code Card -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-primary/10">
                        <i class="text-2xl ti ti-building text-primary"></i>
                    </div>
                    <div class="flex-1">
                        <p class="mb-1 text-sm text-gray-500">Code</p>
                        <p class="text-xl font-bold">{{ $warehouse->code }}</p>
                    </div>
                </div>
            </div>

            <!-- Location Card -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-success/10">
                        <i class="text-2xl ti ti-map-pin text-success"></i>
                    </div>
                    <div class="flex-1">
                        <p class="mb-1 text-sm text-gray-500">Location</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $warehouse->city }}</p>
                        <p class="text-sm text-gray-600">{{ $warehouse->province }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact Card -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-info/10">
                        <i class="text-2xl ti ti-phone text-info"></i>
                    </div>
                    <div class="flex-1">
                        <p class="mb-1 text-sm text-gray-500">Contact</p>
                        @if ($warehouse->phone)
                            <a href="tel:{{ $warehouse->phone }}"
                                class="text-lg font-semibold text-primary hover:underline">
                                {{ $warehouse->phone }}
                            </a>
                        @else
                            <p class="text-lg font-semibold text-gray-400">-</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Section -->
        <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
            <!-- Address Details -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg">
                <h3 class="flex items-center gap-2 pb-4 mb-4 text-lg font-semibold text-gray-900 border-b">
                    <i class="ti ti-map"></i> Address Details
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="mb-1 text-xs font-medium text-gray-500 uppercase">Address</p>
                        <p class="text-sm text-gray-900">{{ $warehouse->address }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="mb-1 text-xs font-medium text-gray-500 uppercase">City</p>
                            <p class="text-sm text-gray-900">{{ $warehouse->city }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-gray-500 uppercase">Province</p>
                            <p class="text-sm text-gray-900">{{ $warehouse->province }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-medium text-gray-500 uppercase">Postal Code</p>
                        <p class="text-sm text-gray-900">{{ $warehouse->postal_code }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact Details -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg">
                <h3 class="flex items-center gap-2 pb-4 mb-4 text-lg font-semibold text-gray-900 border-b">
                    <i class="ti ti-mail"></i> Contact Details
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="mb-1 text-xs font-medium text-gray-500 uppercase">Email</p>
                        @if ($warehouse->email)
                            <a href="mailto:{{ $warehouse->email }}" class="text-sm text-primary hover:underline">
                                {{ $warehouse->email }}
                            </a>
                        @else
                            <p class="text-sm text-gray-400">-</p>
                        @endif
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-medium text-gray-500 uppercase">Phone</p>
                        @if ($warehouse->phone)
                            <a href="tel:{{ $warehouse->phone }}" class="text-sm text-primary hover:underline">
                                {{ $warehouse->phone }}
                            </a>
                        @else
                            <p class="text-sm text-gray-400">-</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Locations Table -->
        @if ($warehouse->locations->isNotEmpty())
            <div class="p-6 bg-white border border-gray-200 rounded-lg">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b">
                    <i class="text-xl ti ti-layout-grid text-gray-700"></i>
                    <h3 class="text-lg font-semibold text-gray-900">Locations</h3>
                    <span class="px-2 py-1 text-xs font-semibold text-white rounded bg-primary">
                        {{ $warehouse->locations->count() }}
                    </span>
                </div>

                <div class="overflow-hidden border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">
                                    Code
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">
                                    Type
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">
                                    Capacity
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($warehouse->locations as $location)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-mono whitespace-nowrap">
                                        {{ $location->code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $typeColors = [
                                                'shelf' => 'bg-blue-100 text-blue-800',
                                                'rack' => 'bg-green-100 text-green-800',
                                                'bin' => 'bg-yellow-100 text-yellow-800',
                                                'floor' => 'bg-purple-100 text-purple-800',
                                                'cold-storage' => 'bg-cyan-100 text-cyan-800',
                                            ];
                                            $color = $typeColors[$location->type] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                            {{ ucfirst($location->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        @if ($location->capacity)
                                            <span class="font-medium text-gray-900">
                                                {{ number_format($location->capacity, 0, ',', '.') }}
                                            </span>
                                            <span class="ml-1 text-gray-500">units</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'inactive' => 'bg-red-100 text-red-800',
                                                'maintenance' => 'bg-yellow-100 text-yellow-800',
                                                'full' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $statusColor =
                                                $statusColors[$location->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                            {{ ucfirst($location->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
