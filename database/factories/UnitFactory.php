<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        $units = ['PCS', 'BOX', 'KG', 'L', 'M', 'ROLL', 'PACK'];

        return [
            'code' => $this->faker->unique()->randomElement($units),
            'name' => $this->faker->word(),
            'symbol' => $this->faker->randomElement($units),
        ];
    }
}
