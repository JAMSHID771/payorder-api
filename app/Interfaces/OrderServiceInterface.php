<?php

namespace App\Interfaces;

use App\Models\Order;

interface OrderServiceInterface
{
    public function index();

    public function getUserOrders($userId);

    public function show($id);

    public function create($data);

    public function update(Order $order, $data);

    public function delete(Order $order);

    public function validateOrderForPayment($orderId, $amount);
    public function markOrderAsPaid($orderId);
}
