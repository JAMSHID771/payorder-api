<?php

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\Repositories\UserRepositoryInterface;
use Carbon\Carbon;

class UserRepository implements UserRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return User::all();
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function findOrFail(int $id): User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->findOrFail($id);
        return $user->update($data);
    }

    public function delete(int $id): bool
    {
        $user = $this->findOrFail($id);
        return $user->delete();
    }

    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }

    public function findByPhoneOrFail(string $phone): User
    {
        return User::where('phone', $phone)->firstOrFail();
    }

    public function createUser(array $data): User
    {
        $user = new User();
        $user->name = $data['name'];
        $user->last_name = $data['last_name'];
        $user->phone = $data['phone'];
        $user->email = $data['email'] ?? null;
        $user->password = $data['password'] ?? null;
        $user->avatar = $data['avatar'] ?? null;
        $user->phone_verification_code = $data['phone_verification_code'];
        $user->phone_verification_expires_at = $data['phone_verification_expires_at'];
        $user->is_verified = false;
        $user->save();
        return $user;
    }

    public function updatePhone(int $userId, string $phone): bool
    {
        $user = $this->findOrFail($userId);
        return $user->update(['phone' => $phone]);
    }

    public function deleteUnverifiedUsers(): int
    {
        return User::where('is_verified', false)
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->delete();
    }

    public function isPhoneExists(string $phone): bool
    {
        return User::where('phone', $phone)->exists();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
