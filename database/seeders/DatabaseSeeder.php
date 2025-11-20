<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Store;
use App\Models\Inventory;
use App\Models\Movement;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario demo
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Tiendas
        $stores = Store::factory()->count(5)->create();

        // Productos
        $products = Product::factory()->count(10)->create();

        // Inventarios (uno por producto y tienda)
        foreach ($stores as $store) {
            foreach ($products as $product) {
                Inventory::factory()->create([
                    'store_id' => $store->id,
                    'product_id' => $product->id,
                ]);
            }
        }

        // Crear inventario con stock bajo (SOLO si no existe)
        $firstStore = $stores->first();
        $firstProduct = $products->first();

        $alreadyExists = Inventory::where('store_id', $firstStore->id)
            ->where('product_id', $firstProduct->id)
            ->first();

        if ($alreadyExists) {
            $alreadyExists->update([
                'quantity' => 1,
                'min_stock' => 5,
            ]);
        } else {
            Inventory::factory()->create([
                'store_id' => $firstStore->id,
                'product_id' => $firstProduct->id,
                'quantity' => 1,
                'min_stock' => 5,
            ]);
        }

        // Movimiento ejemplo (solo si existe Movement)
        if (class_exists(Movement::class)) {
            Movement::create([
                'product_id' => $firstProduct->id,
                'source_store_id' => $stores[0]->id,
                'target_store_id' => $stores[1]->id,
                'quantity' => 2,
                'type' => 'TRANSFER',
            ]);
        }
    }
}
