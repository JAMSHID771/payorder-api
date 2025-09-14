<?php

namespace App\Interfaces\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;
    public function find(int $id): ?Order;
    public function findOrFail(int $id): Order;
    public function create(array $data): Order;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByUser(int $userId): \Illuminate\Database\Eloquent\Collection;
    public function getUserOrders(int $userId): \Illuminate\Database\Eloquent\Collection;
    public function findByStatus(string $status): \Illuminate\Database\Eloquent\Collection;
    public function findPending(): \Illuminate\Database\Eloquent\Collection;
    public function findCompleted(): \Illuminate\Database\Eloquent\Collection;
}
