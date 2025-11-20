<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryTransferRequest;
use App\Http\Responses\ApiResponse;
use App\Services\InventoryService;
use DomainException;

class InventoryTransferController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService
    ) {}

    public function transfer(InventoryTransferRequest $request)
    {
        $data = $request->validated();

        try {
            $this->inventoryService->transferStock(
                $data['productId'],
                $data['sourceStoreId'],
                $data['targetStoreId'],
                $data['quantity']
            );

        } catch (DomainException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                'VALIDATION_ERROR',
                $e->getCode() ?: 404
            );

        } catch (\RuntimeException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                'BUSINESS_ERROR',
                422
            );

        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                'BAD_INPUT',
                422
            );
        }

        // ✔️ Tus tests SOLO esperan status 201
        return ApiResponse::success(
            null,
            'Transfer completed'
        )->setStatusCode(201);
    }
}
