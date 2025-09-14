<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => '998' . fake()->numberBetween(900000000, 999999999),
            'avatar' => null,
            'phone_verified_at' => null,
            'phone_verification_code' => null,
            'phone_verification_expires_at' => null,
            'is_verified' => false,
            'role' => 'user',
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'phone_verified_at' => now(),
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'is_verified' => true,
            'phone_verified_at' => now(),
        ]);
    }
}
