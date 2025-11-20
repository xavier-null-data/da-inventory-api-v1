<?php

namespace App\Strategies\InventoryAlerts;

use Illuminate\Support\Collection;

class StandardAlertStrategy implements AlertStrategyInterface
{
    public function filter(Collection $inventories): Collection
    {
        // Estrategia sencilla: devolver solo los inventarios en alerta
        return $inventories->filter(function ($inventory) {
            return $inventory->quantity <= $inventory->min_stock;
        });
    }
}
