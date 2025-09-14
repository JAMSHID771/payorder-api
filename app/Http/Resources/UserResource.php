<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Check if resource exists and is not false
        if (!$this->resource || $this->resource === false) {
            return [];
        }

        // Make verification code visible for registration and SMS-related responses
        try {
            if (isset($this->phone_verification_code) && 
                $this->phone_verification_code && 
                isset($this->is_verified) && 
                !$this->is_verified) {
                $this->makeVisible(['phone_verification_code', 'phone_verification_expires_at']);
            }
        } catch (\Exception $e) {
            // Ignore errors when checking properties
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'avatar' => $this->avatar ? Storage::disk('public')->url($this->avatar) : null,
            'is_verified' => $this->is_verified,
            'phone_verified_at' => $this->phone_verified_at,
            'role' => $this->role ?? 'user',
            'phone_verification_code' => $this->when(isset($this->phone_verification_code), $this->phone_verification_code),
            'phone_verification_expires_at' => $this->when(isset($this->phone_verification_expires_at), $this->phone_verification_expires_at),
            'token' => $this->when(isset($this->token), $this->token),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
