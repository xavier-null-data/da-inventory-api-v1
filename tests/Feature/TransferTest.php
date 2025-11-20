<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Store;
use App\Models\Product;
use App\Models\Inventory;

class TransferTest extends TestCase
{
    /** @test */
    public function it_can_transfer_inventory_between_stores()
    {
        $product = Product::factory()->create();
        $source = Store::factory()->create();
        $target = Store::factory()->create();

        Inventory::factory()->create([
            'product_id' => $product->id,
            'store_id' => $source->id,
            'quantity' => 20,
        ]);

        $payload = [
            'productId' => $product->id,
            'sourceStoreId' => $source->id,
            'targetStoreId' => $target->id,
            'quantity' => 5,
        ];

        $response = $this->postJson('/api/inventory/transfer', $payload);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_fails_if_stock_is_insufficient()
    {
        $product = Product::factory()->create();
        $source = Store::factory()->create();
        $target = Store::factory()->create();

        Inventory::factory()->create([
            'product_id' => $product->id,
            'store_id' => $source->id,
            'quantity' => 1,
        ]);

        $payload = [
            'productId' => $product->id,
            'sourceStoreId' => $source->id,
            'targetStoreId' => $target->id,
            'quantity' => 10,
        ];

        $response = $this->postJson('/api/inventory/transfer', $payload);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_fails_if_source_store_does_not_exist()
    {
        $product = Product::factory()->create();
        $target = Store::factory()->create();

        $invalidId = '00000000-0000-0000-0000-000000000000';

        $payload = [
            'productId' => $product->id,
            'sourceStoreId' => $invalidId,
            'targetStoreId' => $target->id,
            'quantity' => 1,
        ];

        $response = $this->postJson('/api/inventory/transfer', $payload);

        $response->assertStatus(404);
    }
}
