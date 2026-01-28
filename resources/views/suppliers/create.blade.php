@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('suppliers.index') }}" class="hover:text-blue-600">Suppliers</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">Add Supplier</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Add Supplier</h2>
                <p class="text-sm text-gray-500 mt-1">Add a new business partner to your supplier network.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
            <div class="p-6 sm:p-8">
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 mb-8">
                        <div>
                            <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                Supplier Code <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="ti ti-hash text-lg"></i>
                                </span>
                                <input type="text" id="code" name="code" required 
                                    class="form-control pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="e.g. SUP-001">
                            </div>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Supplier Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="ti ti-user text-lg"></i>
                                </span>
                                <input type="text" id="name" name="name" required 
                                    class="form-control pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="e.g. PT. Global Supply">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="ti ti-mail text-lg"></i>
                                </span>
                                <input type="email" id="email" name="email" 
                                    class="form-control pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="supplier@example.com">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-6 border-t border-gray-100">
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <i class="ti ti-device-floppy mr-2 text-xl"></i>
                            Save Supplier
                        </button>
                        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <i class="ti ti-arrow-left mr-2 text-xl"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
