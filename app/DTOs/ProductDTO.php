<?php

namespace App\DTOs;

class ProductDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?float $price = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $status = 'active',
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            description: $data['description'] ?? null,
            image: $data['image'] ?? null,
            status: $data['status'] ?? 'active',
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description,
            'image' => $this->image,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public static function toArr(array $data): array
    {
        return (new self())->fromArray($data)->toArray();
    }
}
