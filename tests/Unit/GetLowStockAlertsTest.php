<?php

namespace Tests\Unit;

use App\Services\InventoryService;
use App\Repositories\InventoryRepository;
use App\Repositories\MovementRepository;
use App\Strategies\InventoryAlerts\AlertStrategyInterface;
use Illuminate\Database\DatabaseManager;
use Mockery;
use Tests\TestCase;

class GetLowStockAlertsTest extends TestCase
{
    public function test_returns_filtered_alerts()
    {
        $inventoryRepo = Mockery::mock(InventoryRepository::class);
        $movementRepo  = Mockery::mock(MovementRepository::class);
        $db            = Mockery::mock(DatabaseManager::class);
        $alertStrategy = Mockery::mock(AlertStrategyInterface::class);

        $inventories = collect([
            ['product_id' => 'A', 'quantity' => 1, 'min_stock' => 5]
        ]);

        $filtered = collect([
            ['product_id' => 'A', 'quantity' => 1, 'min_stock' => 5]
        ]);

        $inventoryRepo->shouldReceive('getLowStock')->andReturn($inventories);
        $alertStrategy->shouldReceive('filter')->with($inventories)->andReturn($filtered);

        $service = new InventoryService($inventoryRepo, $movementRepo, $alertStrategy);

        $result = $service->getLowStockAlerts();

        $this->assertEquals($filtered, $result);
    }
}
