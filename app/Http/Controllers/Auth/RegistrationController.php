<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterRequest;

class RegistrationController extends Controller
{
    public function store(RegisterRequest $request): UserResource
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => $request->password,
            'email' => $request->email,
        ]);

        UserRegistered::dispatch($user, $request->referral_code);

        return new UserResource($user, true);
    }
}
