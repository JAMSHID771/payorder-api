<?php

namespace App\Interfaces\Repositories;

use App\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;
    public function find(int $id): ?Transaction;
    public function findOrFail(int $id): Transaction;
    public function create(array $data): Transaction;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByOrder(int $orderId): \Illuminate\Database\Eloquent\Collection;
    public function findByStatus(string $status): \Illuminate\Database\Eloquent\Collection;
    public function findSuccessful(): \Illuminate\Database\Eloquent\Collection;
    public function findFailed(): \Illuminate\Database\Eloquent\Collection;
    public function findByPaycomId(string $paycomId): ?Transaction;
    public function getByTimeRange(int $from, int $to): \Illuminate\Database\Eloquent\Collection;
}
