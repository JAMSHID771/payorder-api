<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'price' => $this->faker->numberBetween(10000, 1000000),
            'product_id' => Product::factory(),
            'status' => $this->faker->randomElement(['kutilmoqda', 'jarayonda', 'yakunlandi', 'bekor_qilingan']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'kutilmoqda',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'yakunlandi',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'bekor_qilingan',
        ]);
    }
}
