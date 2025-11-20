<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Store;
use App\Models\Product;
use App\Models\Inventory;

class InventoryTest extends TestCase
{
    /** @test */
    public function it_can_get_inventory_by_store()
    {
        $store = Store::factory()->create();
        Inventory::factory()->count(3)->create(['store_id' => $store->id]);

        $response = $this->getJson("/api/stores/{$store->id}/inventory");

        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_not_found_if_store_does_not_exist()
    {
        $invalidId = '00000000-0000-0000-0000-000000000000';

        $response = $this->getJson("/api/stores/$invalidId/inventory");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_list_low_stock_alerts()
    {
        $product = Product::factory()->create();
        $store = Store::factory()->create();

        Inventory::factory()->create([
            'product_id' => $product->id,
            'store_id' => $store->id,
            'quantity' => 1,
            'min_stock' => 10,
        ]);

        $response = $this->getJson('/api/inventory/alerts');

        $response->assertStatus(200);
    }
}
