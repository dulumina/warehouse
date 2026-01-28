@extends('layouts.modernize')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Add Supplier</h1>
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="mb-4"><label class="block text-sm font-medium mb-2">Name <span
                            class="text-red-500">*</span></label><input type="text" name="code" required
                        class="form-control"></div>
                <div class="mb-4"><label class="block text-sm font-medium mb-2">Name <span
                            class="text-red-500">*</span></label><input type="text" name="name" required
                        class="form-control"></div>
                <div class="mb-4"><label class="block text-sm font-medium mb-2">Email</label><input type="email"
                        name="email" class="form-control"></div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
@endsection
