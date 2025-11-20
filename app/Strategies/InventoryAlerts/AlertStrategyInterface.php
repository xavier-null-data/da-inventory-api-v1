<?php

namespace App\Strategies\InventoryAlerts;

use Illuminate\Support\Collection;

interface AlertStrategyInterface
{
    public function filter(Collection $inventories): Collection;
}
