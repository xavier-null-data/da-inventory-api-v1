<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    // Lista productos
    public function index(Request $request)
    {
        $filters = $request->only([
            'category',
            'min_price',
            'max_price',
            'min_stock',
            'max_stock',
            'per_page',
        ]);

        $products = $this->productService->listWithFilters($filters);

        return ApiResponse::success(
            $products->items(),
            'Listado de productos',
            [
                'page'        => $products->currentPage(),
                'per_page'    => $products->perPage(),
                'total'       => $products->total(),
                'total_pages' => $products->lastPage(),
            ]
        );
    }

    // Obtener producto por UUID
    public function show(string $id)
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return ApiResponse::error(
                'Producto no encontrado',
                'PRODUCT_NOT_FOUND',
                404
            );
        }

        return ApiResponse::success(
            $product,
            'Detalle de producto'
        );
    }

    // Crear producto
    public function store(ProductRequest $request)
    {
        $product = $this->productService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto creado correctamente',
            'data'    => $product,
        ], 201);
    }

    // Actualizar producto
    public function update(ProductRequest $request, string $id)
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return ApiResponse::error(
                'Producto no encontrado',
                'PRODUCT_NOT_FOUND',
                404
            );
        }

        $updated = $this->productService->update($product, $request->validated());

        return ApiResponse::success(
            $updated,
            'Producto actualizado correctamente'
        );
    }

    // Eliminar producto
    public function destroy(string $id)
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return ApiResponse::error(
                'Producto no encontrado',
                'PRODUCT_NOT_FOUND',
                404
            );
        }

        $this->productService->delete($product);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente',
            'data'    => null,
        ], 204);
    }
}
