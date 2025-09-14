<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function uploadAvatar(UploadedFile $file): string
    {
        $filename = 'avatars/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        Storage::disk('public')->put($filename, file_get_contents($file));
        
        return $filename;
    }

    public function deleteAvatar(?string $filename): bool
    {
        if (!$filename) {
            return false;
        }

        return Storage::disk('public')->delete($filename);
    }

    public function getAvatarUrl(?string $filename): ?string
    {
        if (!$filename) {
            return null;
        }

        return Storage::disk('public')->url($filename);
    }

    public function updateAvatar(UploadedFile $newFile, ?string $oldFilename = null): string
    {
        if ($oldFilename) {
            $this->deleteAvatar($oldFilename);
        }

        return $this->uploadAvatar($newFile);
    }
}
