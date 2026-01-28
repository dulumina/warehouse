<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create product');
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'unique:products', 'max:50'],
            'barcode' => ['nullable', 'string', 'unique:products', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'uuid', 'exists:categories,id'],
            'unit_id' => ['required', 'uuid', 'exists:units,id'],
            'type' => ['required', 'in:FINISHED_GOOD,RAW_MATERIAL,CONSUMABLE'],
            'min_stock' => ['required', 'numeric', 'min:0'],
            'max_stock' => ['required', 'numeric', 'min:0'],
            'reorder_point' => ['required', 'numeric', 'min:0'],
            'standard_cost' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'json'],
            'is_batch_tracked' => ['boolean'],
            'is_serial_tracked' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }
}
