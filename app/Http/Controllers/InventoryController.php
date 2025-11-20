<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\InventoryService;
use App\Models\Store;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService
    ) {}

    // GET /stores/{id}/inventory
    public function byStore(string $id)
    {
        if (!Store::find($id)) {
            return ApiResponse::error(
                'Store not found',
                'STORE_NOT_FOUND',
                404
            );
        }

        $inventory = $this->inventoryService->getInventoryByStore($id);

        if ($inventory === null) {
            return ApiResponse::error(
                'Store not found',
                'STORE_NOT_FOUND',
                404
            );
        }

        return ApiResponse::success($inventory, 'Store inventory');
    }

    // GET /inventory/alerts?storeId=
    public function alerts(Request $request)
    {
        $storeId = $request->query('storeId');

        if ($storeId && !Store::find($storeId)) {
            return ApiResponse::error(
                'Store not found',
                'STORE_NOT_FOUND',
                404
            );
        }

        $alerts = $this->inventoryService->getLowStockAlerts($storeId);

        return ApiResponse::success($alerts, 'Low stock alerts');
    }
}
