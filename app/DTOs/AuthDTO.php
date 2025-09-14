<?php

namespace App\DTOs;

class AuthDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $last_name = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?string $avatar = null,
        public ?string $verification_code = null,
        public ?string $code_expires_at = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            last_name: $data['last_name'] ?? null,
            phone: isset($data['phone']) ? self::normalizePhone($data['phone']) : null,
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            avatar: $data['avatar'] ?? null,
            verification_code: $data['verification_code'] ?? null,
            code_expires_at: $data['code_expires_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'password' => $this->password,
            'avatar' => $this->avatar,
            'verification_code' => $this->verification_code,
            'code_expires_at' => $this->code_expires_at
        ];
    }

    public static function toArr(array $data): array
    {
        return (new self())->fromArray($data)->toArray();
    }

    public static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);
        
        if (!str_starts_with($phone, '998') && strlen($phone) === 9) {
            $phone = '998' . $phone;
        }
        
        return $phone;
    }

    public static function generateVerificationCode(): string
    {
        return (string) random_int(10000, 99999);
    }

    public static function getVerificationExpiryTime(): \Carbon\Carbon
    {
        return now()->addMinutes(5);
    }
}
