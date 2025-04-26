<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Images
{
    public static function replaceTmpImage($path): array|string
    {
        return str_replace('tmp/', request()->user()->id.'/', $path);
    }

    public static function processPath($path, $storagePath): string
    {
        $fileName = str_replace('tmp/', '', $path);

        if (! Storage::exists($storagePath.'/'.$fileName)) {
            Storage::copy($path, $storagePath.'/'.$fileName);
            Storage::setVisibility($storagePath.'/'.$fileName, 'public');
        }

        return ltrim($storagePath.'/'.$fileName, '/');
    }

    protected static function getImageUrl($image_path): ?array
    {
        $paths = [];
        if (! is_null($image_path)) {
            foreach ($image_path as $path) {
                $paths[] = Storage::url($path);
            }

            return $paths;
        }

        return null;
    }
}
