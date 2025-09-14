<?php

namespace App\Services;

use App\Interfaces\UserServiceInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\DTOs\AuthDTO;
use App\Services\EskizService;
use App\Services\FileService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\UploadedFile;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EskizService $eskizService,
        private FileService $fileService
    ) {}

    public function index()
    {
        return $this->userRepository->all();
    }

    public function show($id)
    {
        return $this->userRepository->findOrFail($id);
    }

    public function create($data)
    {
        return $this->userRepository->create($data);
    }

    public function update($id, $data)
    {
        $this->userRepository->update($id, $data);
        return $this->userRepository->find($id);
    }

    public function delete($id)
    {
        $user = $this->userRepository->findOrFail($id);
        
        if ($user->avatar) {
            $this->fileService->deleteAvatar($user->avatar);
        }
        
        return $this->userRepository->delete($id);
    }

    public function register($data)
    {
        $userData = AuthDTO::toArr($data);
        
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $userData['avatar'] = $this->fileService->uploadAvatar($data['avatar']);
        }
        
        if ($this->userRepository->isPhoneExists($userData['phone'])) {
            return false;
        }

        $code = AuthDTO::generateVerificationCode();
        $expiresAt = AuthDTO::getVerificationExpiryTime();

        $userData['phone_verification_code'] = $code;
        $userData['phone_verification_expires_at'] = $expiresAt;
        $userData['role'] = 'user'; // Set default role as 'user'

        $user = $this->userRepository->createUser($userData);
        
        $message = $this->buildMessage('register', ['code' => $code]);
        $this->eskizService->sendSms($user->phone, $message);

        return $user;
    }

    public function login($phone)
    {
        $normalizedPhone = AuthDTO::normalizePhone($phone);
        
        $user = $this->userRepository->findByPhone($normalizedPhone);
        
        if (!$user) {
            return false;
        }

        if (!$user->isVerified()) {
            return false;
        }

        $code = AuthDTO::generateVerificationCode();
        $expiresAt = AuthDTO::getVerificationExpiryTime();

        $this->userRepository->update($user->id, [
            'phone_verification_code' => $code,
            'phone_verification_expires_at' => $expiresAt,
        ]);

        $message = $this->buildMessage('login', ['code' => $code]);
        $this->eskizService->sendSms($user->phone, $message);

        return $user;
    }

    public function verifySms($phone, $code)
    {
        $normalizedPhone = AuthDTO::normalizePhone($phone);
        
        $user = $this->userRepository->findByPhone($normalizedPhone);
        
        if (!$user) {
            return false;
        }

        if ($user->isVerificationCodeExpired()) {
            return false;
        }

        if ($user->phone_verification_code !== $code) {
            return false;
        }

        $this->userRepository->update($user->id, [
            'phone_verified_at' => now(),
            'is_verified' => true,
            'phone_verification_code' => null,
            'phone_verification_expires_at' => null,
        ]);

        $user = $this->userRepository->find($user->id);
        $token = $user->createToken('api')->plainTextToken;
        $user->token = $token;

        return $user;
    }

    public function resendSms($phone)
    {
        $normalizedPhone = AuthDTO::normalizePhone($phone);
        
        $user = $this->userRepository->findByPhone($normalizedPhone);
        
        if (!$user) {
            return false;
        }

        $cacheKey = "sms_resend_{$user->phone}";
        if (Cache::has($cacheKey)) {
            return false;
        }

        $code = AuthDTO::generateVerificationCode();
        $expiresAt = AuthDTO::getVerificationExpiryTime();

        $this->userRepository->update($user->id, [
            'phone_verification_code' => $code,
            'phone_verification_expires_at' => $expiresAt,
        ]);

        $message = $this->buildMessage('resend', ['code' => $code]);
        $this->eskizService->sendSms($user->phone, $message);

        Cache::put($cacheKey, true, 60);

        return $this->userRepository->find($user->id);
    }

    public function changePhone($userId, $newPhone)
    {
        $normalizedPhone = AuthDTO::normalizePhone($newPhone);
        
        if ($this->userRepository->isPhoneExists($normalizedPhone)) {
            return false;
        }

        $user = $this->userRepository->findOrFail($userId);

        $rateLimitKey = "sms_rate_limit_{$normalizedPhone}";
        if (Cache::has($rateLimitKey)) {
            return false;
        }

        $code = AuthDTO::generateVerificationCode();
        $expiresAt = AuthDTO::getVerificationExpiryTime();

        $this->userRepository->update($user->id, [
            'phone' => $normalizedPhone,
            'phone_verification_code' => $code,
            'phone_verification_expires_at' => $expiresAt,
            'phone_verified_at' => null,
            'is_verified' => false,
        ]);

        $message = $this->buildMessage('change_phone', ['code' => $code]);
        $this->eskizService->sendSms($normalizedPhone, $message);

        Cache::put($rateLimitKey, true, 60);

        return $this->userRepository->find($user->id);
    }

    public function updateAvatar($userId, UploadedFile $file)
    {
        $user = $this->userRepository->findOrFail($userId);
        
        $avatarPath = $this->fileService->updateAvatar($file, $user->avatar);
        
        $this->userRepository->update($user->id, ['avatar' => $avatarPath]);
        
        return $this->userRepository->find($user->id);
    }

    public function logout($user)
    {
        $user->tokens()->delete();
        return true;
    }

    public function cleanupUnverifiedUsers()
    {
        return $this->userRepository->deleteUnverifiedUsers();
    }

    public function loginWithPassword($email, $password)
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            return false;
        }

        if (!$user->password) {
            return false;
        }

        if (!password_verify($password, $user->password)) {
            return false;
        }

        $token = $user->createToken('api')->plainTextToken;
        $user->token = $token;

        return $user;
    }

    private function buildMessage(string $templateKey, array $data): string
    {
        $template = (string) config("sms.templates.{$templateKey}", '{code}');
        $result = $template;
        foreach ($data as $key => $value) {
            $result = str_replace('{' . $key . '}', (string) $value, $result);
        }
        return $result;
    }
}
