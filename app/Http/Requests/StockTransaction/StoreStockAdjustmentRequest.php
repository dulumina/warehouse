<?php

namespace App\Http\Requests\StockTransaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('adjust inventory');
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'uuid', 'exists:warehouses,id'],
            'type' => ['required', 'in:PHYSICAL_COUNT,CORRECTION,DAMAGED,EXPIRED,FOUND'],
            'adjustment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'uuid', 'exists:products,id'],
            'items.*.location_id' => ['nullable', 'uuid', 'exists:warehouse_locations,id'],
            'items.*.batch_number' => ['nullable', 'string', 'max:255'],
            'items.*.actual_quantity' => ['required', 'numeric', 'min:0'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.reason' => ['nullable', 'string'],
        ];
    }
}
