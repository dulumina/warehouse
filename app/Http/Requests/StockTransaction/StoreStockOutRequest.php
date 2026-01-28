<?php

namespace App\Http\Requests\StockTransaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create stock out');
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'uuid', 'exists:warehouses,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:SALES,RETURN,ADJUSTMENT,PRODUCTION,DAMAGED'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'uuid', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.location_id' => ['nullable', 'uuid', 'exists:warehouse_locations,id'],
            'items.*.batch_number' => ['nullable', 'string', 'max:255'],
            'items.*.serial_number' => ['nullable', 'string', 'max:255'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }
}
