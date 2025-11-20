<?php

namespace Tests\Unit;

use App\Services\InventoryService;
use App\Repositories\InventoryRepository;
use App\Repositories\MovementRepository;
use App\Strategies\InventoryAlerts\AlertStrategyInterface;
use Mockery;
use Tests\TestCase;
use DomainException;
use RuntimeException;

class TransferStockTest extends TestCase
{
    private function makeService($inventoryRepoOverrides = [])
    {
        $inventoryRepo = Mockery::mock(InventoryRepository::class);
        $movementRepo  = Mockery::mock(MovementRepository::class);
        $alertStrategy = Mockery::mock(AlertStrategyInterface::class);

        foreach ($inventoryRepoOverrides as $method => $value) {
            $inventoryRepo->shouldReceive($method)->andReturn($value);
        }

        return new InventoryService(
            $inventoryRepo,
            $movementRepo,
            $alertStrategy
        );
    }

    public function test_throws_exception_on_invalid_quantity()
    {
        // Mock de Store
        Mockery::mock('alias:App\Models\Store')
            ->shouldReceive('find')
            ->andReturn(true);

        $service = $this->makeService();

        $this->expectException(\InvalidArgumentException::class);

        $service->transferStock('prod', 'store1', 'store2', 0);
    }

    public function test_throws_if_source_store_not_exists()
    {
        Mockery::mock('alias:App\Models\Store')
            ->shouldReceive('find')
            ->andReturn(null);

        $service = $this->makeService();

        $this->expectException(DomainException::class);

        $service->transferStock('prod', 'store1', 'store2', 5);
    }

    public function test_throws_if_insufficient_stock()
    {
        // Mock de Store::find para source y target
        Mockery::mock('alias:App\Models\Store')
            ->shouldReceive('find')
            ->andReturn(true);

        $service = $this->makeService([
            'getByProductAndStore' => (object)[
                'quantity'  => 1,
                'min_stock' => 0
            ],
        ]);

        $this->expectException(RuntimeException::class);

        $service->transferStock('prod', 'store1', 'store2', 10);
    }
}
