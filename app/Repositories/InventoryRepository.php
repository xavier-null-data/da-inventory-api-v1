<?php

namespace App\Repositories;

use App\Models\Inventory;
use Illuminate\Support\Collection;

class InventoryRepository
{
    public function getByStore(string $storeId): Collection
    {
        return Inventory::with('product')
            ->where('store_id', $storeId)
            ->get();
    }

    public function getByProductAndStore(string $productId, string $storeId): ?Inventory
    {
        return Inventory::with('product')
            ->where('product_id', $productId)
            ->where('store_id', $storeId)
            ->first();
    }

    public function save(Inventory $inventory): Inventory
    {
        $inventory->save();
        return $inventory->refresh();
    }

    public function getLowStock(?string $storeId = null): Collection
    {
        $query = Inventory::with('product');

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        return $query
            ->whereColumn('quantity', '<=', 'min_stock')
            ->get();
    }
}
