<?php

namespace App\Repositories;

use App\Models\Product;
use App\Interfaces\Repositories\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::all();
    }

    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findOrFail(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $product = $this->findOrFail($id);
        return $product->update($data);
    }

    public function delete(int $id): bool
    {
        $product = $this->findOrFail($id);
        return $product->delete();
    }

    public function findActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('is_active', true)->get();
    }

    public function findByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('category', $category)->get();
    }

    public function search(string $query): \Illuminate\Database\Eloquent\Collection
    {
        if (empty($query)) {
            return $this->all();
        }

        return Product::where('title', 'like', "%{$query}%")->get();
    }

    public function where(string $column, string $operator, $value): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where($column, $operator, $value)->get();
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return Product::query();
    }
}
