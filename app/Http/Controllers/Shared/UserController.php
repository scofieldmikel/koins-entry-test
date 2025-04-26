<?php

namespace App\Http\Controllers\Shared;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function fetchDetailsByEmail(string $email): JsonResponse
    {
        try {
            $user = User::where('email', $email)
                ->where('status', User::Active)
                ->first();

            if (is_null($user)) {
                return $this->badRequestResponse("Sorry, This email address is not valid. Please, use a valid email address. User could not be retrieved!");
            }

            /* Send Back The Resource */
            return $this->okResponse("User Retrieved Successfully!", new UserResource($user));
        } catch (Exception $e) {
            Log::error($e->getMessage(), [$e->getTrace()]);
            return $this->notFoundResponse('Sorry, Something went wrong. Please, try again.');
        }
    }

    public function fetchDetailsById($id): JsonResponse
    {
        try {
            $user = User::where('id', $id)
                ->where('status_id', User::Active)
                ->first();

            if (is_null($user)) {
                return $this->badRequestResponse("Sorry, This email address is not valid. Please, use a valid email address. User could not be retrieved!");
            }

            /* Send Back The Resource */
            return $this->okResponse( "User Retrieved Successfully!", new UserResource($user));
        } catch (Exception $e) {
            Log::error($e->getMessage(), [$e->getTrace()]);
            return $this->notFoundResponse('Sorry, Something went wrong. Please, try again.');
        }
    }
}
