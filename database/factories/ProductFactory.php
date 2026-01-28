<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('SKU-????-####'),
            'barcode' => $this->faker->unique()->ean13(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['FINISHED_GOOD', 'RAW_MATERIAL', 'CONSUMABLE']),
            'min_stock' => $this->faker->numberBetween(10, 50),
            'max_stock' => $this->faker->numberBetween(100, 500),
            'reorder_point' => $this->faker->numberBetween(20, 100),
            'standard_cost' => $this->faker->numberBetween(10000, 500000),
            'selling_price' => $this->faker->numberBetween(15000, 750000),
            'weight' => $this->faker->numberBetween(1, 100),
            'is_batch_tracked' => false,
            'is_serial_tracked' => false,
            'is_active' => true,
        ];
    }
}
