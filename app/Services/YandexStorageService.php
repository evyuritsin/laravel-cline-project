<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class YandexStorageService
{
    public function uploadPhoto(UploadedFile $file, int $inspectionId): string
    {
        $path = "inspections/{$inspectionId}/" . uniqid() . '.' . $file->getClientOriginalExtension();
        
        Storage::disk('yandex')->put($path, file_get_contents($file));
        
        return $path;
    }

    public function deletePhoto(string $path): bool
    {
        return Storage::disk('yandex')->delete($path);
    }

    public function getUrl(string $path): string
    {
        return Storage::disk('yandex')->url($path);
    }

    public function exists(string $path): bool
    {
        return Storage::disk('yandex')->exists($path);
    }
}