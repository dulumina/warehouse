<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create warehouse');
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'unique:warehouses', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'manager_id' => ['nullable', 'uuid', 'exists:users,id'],
            'is_active' => ['boolean'],
        ];
    }
}
