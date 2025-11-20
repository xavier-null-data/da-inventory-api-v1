<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
        // Repository se encarga de hablar con la DB
    }

    public function listWithFilters(array $filters): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;

        Log::info('Listando productos', ['filters' => $filters]);

        return $this->productRepository->search($filters, $perPage);
    }

    public function getById(string $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    public function create(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        return $this->productRepository->update($product, $data);
    }

    public function delete(Product $product): void
    {
        $this->productRepository->delete($product);
    }
}
