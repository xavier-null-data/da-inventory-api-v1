<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Store;
use App\Repositories\InventoryRepository;
use App\Repositories\MovementRepository;
use App\Strategies\InventoryAlerts\AlertStrategyInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    public function __construct(
        private InventoryRepository $inventoryRepository,  // Repo de inventario
        private MovementRepository $movementRepository,    // Repo de movimientos
        private AlertStrategyInterface $alertStrategy      // Estrategia para alertas
    ) {}

    /**
     * Obtener inventario por tienda
     */
    public function getInventoryByStore(string $storeId): ?Collection
    {
        // Validar si la tienda existe
        if (!Store::find($storeId)) {
            return null;
        }

        // Obtener inventario desde el repositorio
        return $this->inventoryRepository->getByStore($storeId);
    }

    /**
     * Transferir stock entre tiendas
     */
    public function transferStock(
        string $productId,
        string $sourceStoreId,
        string $targetStoreId,
        int $quantity
    ): void {

        // Validar que la cantidad sea mayor a cero
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero');
        }

        // Validar tienda de origen
        if (!Store::find($sourceStoreId)) {
            throw new \DomainException('Source store not found', 404);
        }

        // Validar tienda destino
        if (!Store::find($targetStoreId)) {
            throw new \DomainException('Target store not found', 404);
        }

        // Validar producto
        if (!Product::find($productId)) {
            throw new \DomainException('Product not found', 404);
        }

        // Iniciar transacción
        DB::transaction(function () use ($productId, $sourceStoreId, $targetStoreId, $quantity) {

            // Obtener inventario en tienda origen
            $source = $this->inventoryRepository
                ->getByProductAndStore($productId, $sourceStoreId);

            // Validar existencia de inventario en origen
            if (!$source) {
                throw new \RuntimeException('No inventory in source store', 422);
            }

            // Validar stock suficiente
            if ($source->quantity < $quantity) {
                throw new \RuntimeException('Insufficient stock', 422);
            }

            // Restar del inventario origen
            $source->quantity -= $quantity;
            $this->inventoryRepository->save($source);

            // Obtener inventario destino o crearlo
            $target = $this->inventoryRepository
                ->getByProductAndStore($productId, $targetStoreId);

            if (!$target) {
                // Crear registro nuevo con valores base
                $target = new Inventory([
                    'id'         => Str::uuid(),
                    'product_id' => $productId,
                    'store_id'   => $targetStoreId,
                    'min_stock'  => $source->min_stock, // Reutilizar min_stock
                    'quantity'   => 0,
                ]);
            }

            // Sumar al inventario destino
            $target->quantity += $quantity;
            $this->inventoryRepository->save($target);

            // Registrar movimiento de transferencia
            $this->movementRepository->create([
                'product_id'       => $productId,
                'source_store_id'  => $sourceStoreId,
                'target_store_id'  => $targetStoreId,
                'quantity'         => $quantity,
                'type'             => 'TRANSFER',
                'timestamp'        => now(),
            ]);
        });
    }

    /**
     * Obtener lista de alertas de inventario bajo
     */
    public function getLowStockAlerts(?string $storeId = null): Collection
    {
        // Obtener inventarios con bajo stock desde el repositorio
        $inventories = $this->inventoryRepository->getLowStock($storeId);

        // Filtrar usando la estrategia seleccionada
        return $this->alertStrategy->filter($inventories);
    }
}
