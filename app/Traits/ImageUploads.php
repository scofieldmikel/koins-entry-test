<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait ImageUploads
{
    public static function uploadSingleImage(object $file, string $dir = ''): string
    {
        $path = Storage::disk('s3')
            ->put($dir, $file, 'public');
        return $path;
    }

    public static function imageExists(string $path): bool
    {
        if (Storage::disk('s3')->exists($path)) {
            return true;
        }

        return false;
    }

    public static function deleteFile(string $path): bool
    {
        $checkStatus = Self::imageExists($path);
        if ($checkStatus) {
            $status = Storage::disk('s3')->delete($path);
            return $status;
        }

        return false;
    }
}
