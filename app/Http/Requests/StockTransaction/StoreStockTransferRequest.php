<?php

namespace App\Http\Requests\StockTransaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create stock transfer');
    }

    public function rules(): array
    {
        return [
            'from_warehouse_id' => ['required', 'uuid', 'exists:warehouses,id'],
            'to_warehouse_id' => ['required', 'uuid', 'exists:warehouses,id', 'different:from_warehouse_id'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'uuid', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.from_location_id' => ['nullable', 'uuid', 'exists:warehouse_locations,id'],
            'items.*.to_location_id' => ['nullable', 'uuid', 'exists:warehouse_locations,id'],
            'items.*.batch_number' => ['nullable', 'string', 'max:255'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }
}
