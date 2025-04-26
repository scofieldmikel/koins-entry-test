<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\Images;
use App\Jobs\MoveImage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\ProfileRequest;

class DashboardController extends Controller
{
    public function updateProfile(ProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update([
            'profile_image' => Images::replaceTmpImage($request->image),
            'gender' => $request->gender,
            'phone' => $request->phone,
        ]);

        dispatch(new MoveImage($user, [$request->image]));

        return $this->okResponse('Image Uploaded Successfully');
    }

    public function getProfile(Request $request)
    {
        return new UserResource($request->user());
    }
}
