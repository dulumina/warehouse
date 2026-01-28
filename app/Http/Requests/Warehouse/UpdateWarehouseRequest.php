<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit warehouse');
    }

    public function rules(): array
    {
        return [
            'code' => ['string', 'unique:warehouses,code,' . $this->warehouse->id, 'max:50'],
            'name' => ['string', 'max:255'],
            'address' => ['string'],
            'city' => ['string', 'max:100'],
            'province' => ['string', 'max:100'],
            'postal_code' => ['string', 'max:20'],
            'phone' => ['string', 'max:20'],
            'email' => ['email', 'max:255'],
            'manager_id' => ['nullable', 'uuid', 'exists:users,id'],
            'is_active' => ['boolean'],
        ];
    }
}
