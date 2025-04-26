<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only(['email', 'password']))) {
            return new UserResource(auth()->user(), true);
        }
        $user = User::withTrashed()->whereNotNull('deleted_at')->where('email', $request->email)->first();

        if (! $user) {
            return $this->notFoundResponse('Invalid credentials');
        }

        if ($user->deleted_at !== null) {
            return $this->badRequestResponse('User account is disabled.');
        }

        if (! Hash::check($request->password, $user->password)) {
            return $this->notFoundResponse('Incorrect password');
        }

        if ($user->status === false) {
            return $this->badRequestResponse('User account is not activated.');
        }

        return $this->notFoundResponse('Invalid Credentials');
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();

        return $this->okResponse('Logout Successful');
    }
}
