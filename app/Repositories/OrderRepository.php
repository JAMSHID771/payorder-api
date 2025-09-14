<?php

namespace App\Repositories;

use App\Models\Order;
use App\Interfaces\Repositories\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Order::all();
    }

    public function find(int $id): ?Order
    {
        return Order::find($id);
    }

    public function findOrFail(int $id): Order
    {
        return Order::findOrFail($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $order = $this->findOrFail($id);
        return $order->update($data);
    }

    public function delete(int $id): bool
    {
        $order = $this->findOrFail($id);
        return $order->delete();
    }

    public function findByUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return Order::where('user_id', $userId)->get();
    }

    public function getUserOrders(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return Order::where('user_id', $userId)->get();
    }

    public function findByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Order::where('status', $status)->get();
    }

    public function findPending(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->findByStatus('kutilmoqda');
    }

    public function findCompleted(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->findByStatus('completed');
    }
}
