<?php

namespace App\Services;

use App\Interfaces\OrderServiceInterface;
use App\Interfaces\Repositories\OrderRepositoryInterface;
use App\Models\Order;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function index()
    {
        return $this->orderRepository->all()->load('product');
    }

    public function getUserOrders($userId)
    {
        return $this->orderRepository->getUserOrders($userId)->load('product');
    }

    public function show($id)
    {
        $order = $this->orderRepository->findOrFail($id);
        return $order->load('product');
    }

    public function create($data)
    {
        $order = $this->orderRepository->create($data);
        return $order->load('product');
    }

    public function update(Order $order, $data)
    {
        $updateData = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });

        $this->orderRepository->update($order->id, $updateData);
        $order = $this->orderRepository->find($order->id);
        return $order->load('product');
    }

    public function delete(Order $order)
    {
        return $this->orderRepository->delete($order->id);
    }

    public function validateOrderForPayment($orderId, $amount)
    {
        $order = $this->orderRepository->find($orderId);
        
        if (!$order) {
            return [
                'success' => false,
                'error' => [
                    'code' => -31050,
                    'message' => 'Buyurtma topilmadi'
                ]
            ];
        }

        if ($order->price * 100 != $amount) {
            return [
                'success' => false,
                'error' => [
                    'code' => -31001,
                    'message' => 'Summada xatolik'
                ]
            ];
        }

        return [
            'success' => true,
            'order' => $order
        ];
    }

    public function markOrderAsPaid($orderId)
    {
        return $this->orderRepository->update($orderId, ['status' => 'paid']);
    }
}
