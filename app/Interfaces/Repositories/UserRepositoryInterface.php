<?php

namespace App\Interfaces\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;
    public function find(int $id): ?User;
    public function findOrFail(int $id): User;
    public function create(array $data): User;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByPhone(string $phone): ?User;
    public function findByPhoneOrFail(string $phone): User;
    public function createUser(array $data): User;
    public function updatePhone(int $userId, string $phone): bool;
    public function deleteUnverifiedUsers(): int;
    public function isPhoneExists(string $phone): bool;
    public function findByEmail(string $email): ?User;
}
