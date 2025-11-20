<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryTransferController;

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('{id}', [ProductController::class, 'show']);
    Route::post('/', [ProductController::class, 'store']);
    Route::put('{id}', [ProductController::class, 'update']);
    Route::delete('{id}', [ProductController::class, 'destroy']);
});

Route::get('stores/{id}/inventory', [InventoryController::class, 'byStore']);

Route::post('inventory/transfer', [InventoryTransferController::class, 'transfer']);
Route::get('inventory/alerts', [InventoryController::class, 'alerts']);
