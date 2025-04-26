<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\Reference;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ImageRequest;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function UploadImage(ImageRequest $request): JsonResponse
    {
        $mimeType = $request->file('image')->getMimeType();

        if (Str::startsWith($mimeType, 'image/')) {
            $image = Image::make($request->file('image'))->widen(1000);
            $streamedData = $image->stream('jpg');
            $path = 'tmp/'.Reference::getHashedToken(rand(10, 25));
            Storage::put($path, (string) $streamedData, 'public');
        } else {
            $path = Storage::putFileAs('tmp', $request->file('image'), Reference::getHashedToken(rand(10, 25)));
        }

        return $this->okResponse('File Uploaded Successfully', [
            'path' => $path,
        ]);
    }
}
