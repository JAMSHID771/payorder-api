<?php

namespace App\DTOs;

class TransactionDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $paycom_transaction_id = null,
        public ?int $paycom_time = null,
        public ?int $amount = null,
        public ?int $state = null,
        public ?string $reason = null,
        public ?int $order_id = null,
        public ?int $perform_time_unix = null,
        public ?int $cancel_time = null,
        public ?string $create_time = null,
        public ?string $perform_time = null,
        public ?string $paycom_time_datetime = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            paycom_transaction_id: $data['paycom_transaction_id'] ?? null,
            paycom_time: $data['paycom_time'] ?? null,
            amount: $data['amount'] ?? null,
            state: $data['state'] ?? null,
            reason: $data['reason'] ?? null,
            order_id: $data['order_id'] ?? null,
            perform_time_unix: $data['perform_time_unix'] ?? null,
            cancel_time: $data['cancel_time'] ?? null,
            create_time: $data['create_time'] ?? null,
            perform_time: $data['perform_time'] ?? null,
            paycom_time_datetime: $data['paycom_time_datetime'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'paycom_transaction_id' => $this->paycom_transaction_id,
            'paycom_time' => $this->paycom_time,
            'amount' => $this->amount,
            'state' => $this->state,
            'reason' => $this->reason,
            'order_id' => $this->order_id,
            'perform_time_unix' => $this->perform_time_unix,
            'cancel_time' => $this->cancel_time,
            'create_time' => $this->create_time,
            'perform_time' => $this->perform_time,
            'paycom_time_datetime' => $this->paycom_time_datetime,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public static function toArr(array $data): array
    {
        return (new self())->fromArray($data)->toArray();
    }
}
