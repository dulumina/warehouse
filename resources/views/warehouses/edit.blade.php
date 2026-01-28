@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Warehouse</h1>

        <div class="bg-white rounded-xl shadow p-6 max-w-2xl">
            <form action="{{ route('warehouses.update', $warehouse) }}" method="POST">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="form-control w-full">
                        <label class="label" for="code">
                            <span class="label-text font-medium">Code <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text" id="code" name="code" value="{{ old('code', $warehouse->code) }}"
                            required class="input input-bordered w-full @error('code') input-error @enderror">
                        @error('code')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label" for="name">
                            <span class="label-text font-medium">Name <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $warehouse->name) }}"
                            required class="input input-bordered w-full @error('name') input-error @enderror">
                        @error('name')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <div class="form-control w-full mb-6">
                    <label class="label" for="address">
                        <span class="label-text font-medium">Address <span class="text-red-500">*</span></span>
                    </label>
                    <input type="text" id="address" name="address" value="{{ old('address', $warehouse->address) }}"
                        required class="input input-bordered w-full @error('address') input-error @enderror">
                    @error('address')
                        <label class="label">
                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="form-control w-full">
                        <label class="label" for="city">
                            <span class="label-text font-medium">City <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text" id="city" name="city" value="{{ old('city', $warehouse->city) }}"
                            required class="input input-bordered w-full @error('city') input-error @enderror">
                        @error('city')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label" for="province">
                            <span class="label-text font-medium">Province <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text" id="province" name="province"
                            value="{{ old('province', $warehouse->province) }}" required
                            class="input input-bordered w-full @error('province') input-error @enderror">
                        @error('province')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label" for="postal_code">
                            <span class="label-text font-medium">Postal Code <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text" id="postal_code" name="postal_code"
                            value="{{ old('postal_code', $warehouse->postal_code) }}" required
                            class="input input-bordered w-full @error('postal_code') input-error @enderror">
                        @error('postal_code')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <div class="form-control w-full">
                        <label class="label" for="phone">
                            <span class="label-text font-medium">Phone</span>
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $warehouse->phone) }}"
                            class="input input-bordered w-full @error('phone') input-error @enderror">
                        @error('phone')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label" for="email">
                            <span class="label-text font-medium">Email</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $warehouse->email) }}"
                            class="input input-bordered w-full @error('email') input-error @enderror">
                        @error('email')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check mr-2"></i> Update Warehouse
                    </button>
                    <a href="{{ route('warehouses.index') }}" class="btn btn-ghost">
                        <i class="ti ti-x mr-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
