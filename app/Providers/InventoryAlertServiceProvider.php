<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Strategies\InventoryAlerts\AlertStrategyInterface;
use App\Strategies\InventoryAlerts\StandardAlertStrategy;

class InventoryAlertServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            AlertStrategyInterface::class,
            StandardAlertStrategy::class
        );
    }
}
