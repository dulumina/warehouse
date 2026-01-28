<?php

namespace App\Http\Requests\StockTransaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create stock in');
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'uuid', 'exists:warehouses,id'],
            'supplier_id' => ['nullable', 'uuid', 'exists:suppliers,id'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:PURCHASE,RETURN,ADJUSTMENT,PRODUCTION'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'uuid', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.location_id' => ['nullable', 'uuid', 'exists:warehouse_locations,id'],
            'items.*.batch_number' => ['nullable', 'string', 'max:255'],
            'items.*.expiry_date' => ['nullable', 'date'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }
}
