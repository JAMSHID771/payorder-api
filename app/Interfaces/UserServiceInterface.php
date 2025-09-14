<?php

namespace App\Interfaces;

use Illuminate\Http\UploadedFile;

interface UserServiceInterface
{
    public function index();
    public function show($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    
        public function register($data);
    public function login($phone);
    public function verifySms($phone, $code);
    public function resendSms($phone);
    public function changePhone($userId, $newPhone);
    public function updateAvatar($userId, UploadedFile $file);
    public function logout($user);

    public function cleanupUnverifiedUsers();
    public function loginWithPassword($email, $password);
}
