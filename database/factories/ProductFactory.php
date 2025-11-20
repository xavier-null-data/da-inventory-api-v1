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
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['general', 'electrónica', 'hogar']),
            'price' => $this->faker->randomFloat(2, 5, 500),
            'sku' => 'SKU-' . strtoupper($this->faker->bothify('???-###')),
        ];
    }
}
