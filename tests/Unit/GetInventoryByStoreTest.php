<?php

namespace Tests\Unit;

use App\Services\InventoryService;
use App\Repositories\InventoryRepository;
use App\Repositories\MovementRepository;
use App\Strategies\InventoryAlerts\AlertStrategyInterface;
use Illuminate\Database\DatabaseManager;
use Mockery;
use Tests\TestCase;

class GetInventoryByStoreTest extends TestCase
{
    public function test_returns_null_if_store_not_found()
    {
        $inventoryRepo = Mockery::mock(InventoryRepository::class);
        $movementRepo  = Mockery::mock(MovementRepository::class);
        $db            = Mockery::mock(DatabaseManager::class);
        $alertStrategy = Mockery::mock(AlertStrategyInterface::class);

        // Mock del modelo Store antes de instanciar el servicio
        Mockery::mock('alias:App\Models\Store')
            ->shouldReceive('find')
            ->with('fake-id')
            ->andReturn(null);

        // Si no existe tienda, nunca debería consultarse el inventario
        $inventoryRepo->shouldReceive('getByStore')->never();

        $service = new InventoryService(
            $inventoryRepo,
            $movementRepo,
            $alertStrategy
        );

        $result = $service->getInventoryByStore('fake-id');

        $this->assertNull($result);
    }


    public function test_returns_inventory_when_store_exists()
    {
        $inventoryRepo = Mockery::mock(InventoryRepository::class);
        $movementRepo  = Mockery::mock(MovementRepository::class);
        $db            = Mockery::mock(DatabaseManager::class);
        $alertStrategy = Mockery::mock(AlertStrategyInterface::class);

        $fakeInventory = collect([['id' => 1]]);

        // Mock del Store existente
        Mockery::mock('alias:App\Models\Store')
            ->shouldReceive('find')
            ->with('123')
            ->andReturn(true);

        // El repositorio debe devolver inventario
        $inventoryRepo->shouldReceive('getByStore')
            ->with('123')
            ->andReturn($fakeInventory);

        $service = new InventoryService(
            $inventoryRepo,
            $movementRepo,
            $alertStrategy
        );

        $result = $service->getInventoryByStore('123');

        $this->assertEquals($fakeInventory, $result);
    }
}
