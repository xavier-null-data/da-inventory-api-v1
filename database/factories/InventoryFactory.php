<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        return [
            // Usa factories que ya generan UUID correctos
            'product_id' => Product::factory()->create()->id,
            'store_id' => Store::factory()->create()->id,
            'quantity' => $this->faker->numberBetween(0, 50),
            'min_stock' => $this->faker->numberBetween(1, 10),
        ];
    }
}
