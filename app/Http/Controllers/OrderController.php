<?php

namespace App\Http\Controllers;

use App\DTOs\OrderDTO;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Interfaces\OrderServiceInterface;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderServiceInterface $orderService
    ) {}

    public function index()
    {
        $orders = $this->orderService->index();
        return $this->success(
            OrderResource::collection($orders),
            'Buyurtmalar muvaffaqiyatli olindi'
        );
    }

    public function myOrders(Request $request)
    {
        $userId = $request->user()->id;
        $orders = $this->orderService->getUserOrders($userId);
        return $this->success(
            OrderResource::collection($orders),
            'Foydalanuvchi buyurtmalari muvaffaqiyatli olindi'
        );
    }

    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;
        $orderDTO = OrderDTO::fromArray($validated);
        $order = $this->orderService->create($orderDTO->toArray());

        return $this->success(
            new OrderResource($order),
            'Buyurtma muvaffaqiyatli yaratildi',
            201
        );
    }

    public function show(string $id)
    {
        $order = $this->orderService->show($id);
        return $this->success(
            new OrderResource($order),
            'Buyurtma muvaffaqiyatli olindi'
        );
    }

    public function update(UpdateOrderRequest $request, string $id)
    {
        $order = $this->orderService->show($id);
        $orderDTO = OrderDTO::fromArray($request->validated());
        $updatedOrder = $this->orderService->update($order, $orderDTO->toArray());

        return $this->success(
            new OrderResource($updatedOrder),
            'Buyurtma muvaffaqiyatli yangilandi'
        );
    }

    public function destroy(string $id)
    {
        $order = $this->orderService->show($id);
        $this->orderService->delete($order);

        return $this->success(
            [],
            'Buyurtma muvaffaqiyatli ochirildi'
        );
    }
}
