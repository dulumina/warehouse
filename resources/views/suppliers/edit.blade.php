@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Supplier</h1>
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4"><label class="block text-sm font-medium mb-2">Code</label><input type="text"
                        name="code" value="{{ $supplier->code }}" class="form-control"></div>
                <div class="mb-4"><label class="block text-sm font-medium mb-2">Name</label><input type="text"
                        name="name" value="{{ $supplier->name }}" class="form-control"></div>
                <div class="mb-4"><label class="block text-sm font-medium mb-2">Email</label><input type="email"
                        name="email" value="{{ $supplier->email }}" class="form-control"></div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@endsection
