<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\VerifySmsRequest;
use App\Http\Requests\Auth\ResendSmsRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserServiceInterface;
use App\DTOs\AuthDTO;

class AuthController extends Controller
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function register(RegisterRequest $request)
    {
        $authDTO = AuthDTO::fromArray($request->validated());
        $user = $this->userService->register($authDTO->toArray());

        if (!$user) {
            return $this->error('Telefon raqami allaqachon mavjud', 400);
        }

        return $this->success(
            new UserResource($user)
        );
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $user = $this->userService->loginWithPassword($validated['email'], $validated['password']);
        
        if (!$user) {
            return $this->error('Notogri malumotlar', 401);
        }
        
        return $this->success(
            new UserResource($user)
        );
    }

    public function verifySms(VerifySmsRequest $request)
    {
        $validated = $request->validated();
        $user = $this->userService->verifySms($validated['phone'], $validated['code']);

        if (!$user) {
            return $this->error('Notogri tasdiqlash kodi', 400);
        }

        return $this->success(
            new UserResource($user)
        );
    }

    public function resendSms(ResendSmsRequest $request)
    {
        $user = $this->userService->resendSms($request->phone);

        if (!$user) {
            return $this->error('SMS qayta yuborish mumkin emas', 400);
        }

        return $this->success(
            new UserResource($user)
        );
    }
}
