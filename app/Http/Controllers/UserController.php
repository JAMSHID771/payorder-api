<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePhoneRequest;
use App\Http\Requests\User\UpdateAvatarRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserServiceInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function profile(Request $request)
    {
        $user = $request->user();
        
        return $this->success(
            new UserResource($user),
            'Profil malumotlari'
        );
    }

    public function changePhone(ChangePhoneRequest $request)
    {
        $user = $request->user();
        $newPhone = $request->validated()['phone'];
        $updatedUser = $this->userService->changePhone($user->id, $newPhone);

        return $this->success(
            new UserResource($updatedUser),
            'Yangi raqamga tasdiqlovchi kod yuborildi'
        );
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $user = $request->user();
        $updatedUser = $this->userService->updateAvatar($user->id, $request->file('avatar'));

        return $this->success(
            new UserResource($updatedUser),
            'Avatar muvaffaqiyatli yangilandi'
        );
    }

    public function logout(Request $request)
    {
        $this->userService->logout($request->user());

        return $this->success(null, 'Tizimdan chiqish muvaffaqiyatli');
    }
}
