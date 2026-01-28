<?php

namespace Database\Factories;

use App\Models\StockIn;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockInFactory extends Factory
{
    protected $model = StockIn::class;

    public function definition(): array
    {
        return [
            'document_number' => $this->faker->unique()->bothify('SI-????????????'),
            'transaction_date' => now()->date(),
            'type' => $this->faker->randomElement(['PURCHASE', 'RETURN', 'ADJUSTMENT', 'PRODUCTION']),
            'status' => 'DRAFT',
            'total_items' => 0,
            'total_quantity' => 0,
            'total_value' => 0,
        ];
    }
}
