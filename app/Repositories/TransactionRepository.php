<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Interfaces\Repositories\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Transaction::all();
    }

    public function find(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function findOrFail(int $id): Transaction
    {
        return Transaction::findOrFail($id);
    }

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $transaction = $this->findOrFail($id);
        return $transaction->update($data);
    }

    public function delete(int $id): bool
    {
        $transaction = $this->findOrFail($id);
        return $transaction->delete();
    }

    public function findByOrder(int $orderId): \Illuminate\Database\Eloquent\Collection
    {
        return Transaction::where('order_id', $orderId)->get();
    }

    public function findByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Transaction::where('status', $status)->get();
    }

    public function findSuccessful(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->findByStatus('success');
    }

    public function findFailed(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->findByStatus('failed');
    }

    public function findByPaycomId(string $paycomId): ?Transaction
    {
        return Transaction::where('paycom_transaction_id', $paycomId)->first();
    }

    public function getByTimeRange(int $from, int $to): \Illuminate\Database\Eloquent\Collection
    {
        return Transaction::whereBetween('paycom_time', [$from, $to])->get();
    }
}
