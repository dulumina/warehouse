@extends('layouts.modernize')

@section('content')
    <div class="container-fluid px-4 py-6">
        <!-- Breadcrumb -->
        <nav class="flex text-sm text-gray-600 mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="{{ route('warehouses.index') }}" class="hover:text-blue-600 transition-colors">
                        <i class="ti ti-home text-lg"></i>
                    </a>
                </li>
                <li><i class="ti ti-chevron-right text-xs"></i></li>
                <li><a href="{{ route('warehouses.index') }}" class="hover:text-blue-600 transition-colors">Warehouses</a></li>
                <li><i class="ti ti-chevron-right text-xs"></i></li>
                <li class="text-gray-900 font-medium">Add New</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Add New Warehouse</h1>
            <p class="text-gray-600">Create a new storage location for inventory management</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form - 2/3 width -->
            <div class="lg:col-span-2">
                <form action="{{ route('warehouses.store') }}" method="POST" id="warehouseForm">
                    @csrf

                    <!-- Basic Information Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-600 rounded-lg p-2">
                                    <i class="ti ti-building text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Basic Information</h3>
                                    <p class="text-sm text-gray-600">Warehouse identification details</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Warehouse Code -->
                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Warehouse Code <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-barcode text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                            id="code" 
                                            name="code" 
                                            value="{{ old('code') }}" 
                                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('code') border-red-500 @enderror"
                                            placeholder="e.g., WH-001, WH-MAIN"
                                            required>
                                    </div>
                                    @error('code')
                                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                            <i class="ti ti-alert-circle"></i>{{ $message }}
                                        </p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Unique identifier for the warehouse</p>
                                </div>

                                <!-- Warehouse Name -->
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Warehouse Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-building-warehouse text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                            id="name" 
                                            name="name" 
                                            value="{{ old('name') }}" 
                                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                                            placeholder="e.g., Main Warehouse, Central Hub"
                                            required>
                                    </div>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                            <i class="ti ti-alert-circle"></i>{{ $message }}
                                        </p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Full name of the warehouse</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Details Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-600 rounded-lg p-2">
                                    <i class="ti ti-map-pin text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Location Details</h3>
                                    <p class="text-sm text-gray-600">Physical address information</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-6">
                                <!-- Street Address -->
                                <div>
                                    <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Street Address <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-map text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                            id="address" 
                                            name="address" 
                                            value="{{ old('address') }}" 
                                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 @error('address') border-red-500 @enderror"
                                            placeholder="Street name, building number, etc."
                                            required>
                                    </div>
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                            <i class="ti ti-alert-circle"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- City, Province, Postal Code -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- City -->
                                    <div>
                                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                                            City <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="ti ti-building-community text-gray-400"></i>
                                            </div>
                                            <input type="text" 
                                                id="city" 
                                                name="city" 
                                                value="{{ old('city') }}" 
                                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 @error('city') border-red-500 @enderror"
                                                placeholder="City"
                                                required>
                                        </div>
                                        @error('city')
                                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                                <i class="ti ti-alert-circle"></i>{{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Province -->
                                    <div>
                                        <label for="province" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Province/State <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="ti ti-map-2 text-gray-400"></i>
                                            </div>
                                            <input type="text" 
                                                id="province" 
                                                name="province" 
                                                value="{{ old('province') }}" 
                                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 @error('province') border-red-500 @enderror"
                                                placeholder="Province"
                                                required>
                                        </div>
                                        @error('province')
                                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                                <i class="ti ti-alert-circle"></i>{{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Postal Code -->
                                    <div>
                                        <label for="postal_code" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Postal Code <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="ti ti-mail text-gray-400"></i>
                                            </div>
                                            <input type="text" 
                                                id="postal_code" 
                                                name="postal_code" 
                                                value="{{ old('postal_code') }}" 
                                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 @error('postal_code') border-red-500 @enderror"
                                                placeholder="12345"
                                                required>
                                        </div>
                                        @error('postal_code')
                                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                                <i class="ti ti-alert-circle"></i>{{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-6 py-4 border-b border-orange-200">
                            <div class="flex items-center gap-3">
                                <div class="bg-orange-600 rounded-lg p-2">
                                    <i class="ti ti-phone text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Contact Information</h3>
                                    <p class="text-sm text-gray-600">Optional communication details</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Phone Number
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-phone text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                            id="phone" 
                                            name="phone" 
                                            value="{{ old('phone') }}" 
                                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50"
                                            placeholder="+62 812-3456-7890">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Contact number for this warehouse</p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email Address
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ti ti-mail text-gray-400"></i>
                                        </div>
                                        <input type="email" 
                                            id="email" 
                                            name="email" 
                                            value="{{ old('email') }}" 
                                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50"
                                            placeholder="warehouse@company.com">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Email for warehouse inquiries</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('warehouses.index') }}" 
                               class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition-all inline-flex items-center gap-2">
                                <i class="ti ti-x"></i>
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all inline-flex items-center gap-2">
                                <i class="ti ti-device-floppy"></i>
                                Save Warehouse
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sidebar - 1/3 width -->
            <div class="lg:col-span-1">
                <!-- All fields required info -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="ti ti-info-circle text-blue-600 text-xl mt-0.5"></i>
                        <div>
                            <h4 class="font-semibold text-blue-900 mb-1">Required Fields</h4>
                            <p class="text-sm text-blue-700">All fields marked with * are required</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-4 py-3 border-b border-purple-200">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-bulb text-purple-600 text-lg"></i>
                            <h4 class="font-bold text-gray-900">Quick Tips</h4>
                        </div>
                    </div>
                    <div class="p-4">
                        <ul class="space-y-2.5 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <i class="ti ti-check text-green-600 mt-0.5"></i>
                                <span>Use clear, descriptive names</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ti ti-check text-green-600 mt-0.5"></i>
                                <span>Warehouse codes should be unique</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ti ti-check text-green-600 mt-0.5"></i>
                                <span>Complete address helps in logistics</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="ti ti-check text-green-600 mt-0.5"></i>
                                <span>Add contact info for coordination</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Required Checklist -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-checklist text-gray-700 text-lg"></i>
                            <h4 class="font-bold text-gray-900">Required Checklist</h4>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="space-y-2.5">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <span class="text-gray-700">Warehouse Code</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <span class="text-gray-700">Warehouse Name</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <span class="text-gray-700">Street Address</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <span class="text-gray-700">City</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <span class="text-gray-700">Province/State</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <span class="text-gray-700">Postal Code</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Form validation enhancement
        document.getElementById('warehouseForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });

        // Remove error styling on input
        document.querySelectorAll('input[required]').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('border-red-500');
                }
            });
        });
    </script>
    @endpush
@endsection