<?php

namespace App\Interfaces\Repositories;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;
    public function find(int $id): ?Product;
    public function findOrFail(int $id): Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findActive(): \Illuminate\Database\Eloquent\Collection;
    public function findByCategory(string $category): \Illuminate\Database\Eloquent\Collection;
    public function search(string $query): \Illuminate\Database\Eloquent\Collection;
}
