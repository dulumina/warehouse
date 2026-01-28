@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('stock-adjustments.index') }}" class="hover:text-blue-600">Stock Adjustments</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">Create Stock Adjustment</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Create Stock Adjustment</h2>
                <p class="text-sm text-gray-500 mt-1">Adjust stock levels due to damage, loss, or other reasons.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4">
                        <i class="ti ti-adjustments text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Stock Adjustment Form</h3>
                    <p class="text-gray-500 max-w-sm mb-8">This feature is currently under development or requires a specific form component.</p>
                    <div class="flex gap-4">
                        <a href="{{ route('stock-adjustments.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <i class="ti ti-arrow-left mr-2 text-xl"></i>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
