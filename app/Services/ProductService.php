<?php

namespace App\Services;

use App\Interfaces\ProductServiceInterface;
use App\Interfaces\Repositories\ProductRepositoryInterface;
use App\Models\Product;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function index()
    {
        return $this->productRepository->all();
    }

    public function show($id)
    {
        return $this->productRepository->findOrFail($id);
    }

    public function create($data)
    {
        return $this->productRepository->create($data);
    }

    public function update(Product $product, $data)
    {
        $this->productRepository->update($product->id, $data);
        return $this->productRepository->find($product->id);
    }

    public function delete(Product $product)
    {
        return $this->productRepository->delete($product->id);
    }

    public function search($query)
    {
        if (empty($query)) {
            return $this->productRepository->all();
        }

        return $this->productRepository->search($query);
    }
}
