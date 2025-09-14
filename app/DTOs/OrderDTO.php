<?php

namespace App\DTOs;

class OrderDTO
{
    public function __construct(
        public ?int $id = null,
        public ?float $price = null,
        public ?string $status = 'kutilmoqda',
        public ?int $product_id = null,
        public ?int $user_id = null,
        public ?array $product = null,
        public ?array $user = null,
        public ?string $notes = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            status: $data['status'] ?? 'kutilmoqda',
            product_id: $data['product_id'] ?? null,
            user_id: $data['user_id'] ?? null,
            product: $data['product'] ?? null,
            user: $data['user'] ?? null,
            notes: $data['notes'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'status' => $this->status,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'product' => $this->product,
            'user' => $this->user,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public static function toArr(array $data): array
    {
        return (new self())->fromArray($data)->toArray();
    }
}
