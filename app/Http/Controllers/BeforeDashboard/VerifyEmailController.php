<?php

namespace App\Http\Controllers\BeforeDashboard;

use Illuminate\Http\Request;
use App\Services\TotpService;
use App\Mail\Auth\VerifyEmail;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\VerifyEmailRequest;

class VerifyEmailController extends Controller
{
    public function resend(Request $request): JsonResponse
    {
        if ($this->checkIfVerified($request->user())) {
            $this->sendToken($request->user());

            return $this->okResponse('Token Has Been Resent');
        }

        return $this->forbiddenResponse('Email Has Already Been Validated');
    }

    public function verify(VerifyEmailRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($this->checkIfVerified($user)) {
            $user->update([
                'email_verified_at' => now(),
                'status' => true
            ]);

            return $this->okResponse('Code Validated Successfully', new UserResource($request->user()));
        }

        return $this->forbiddenResponse('Email Has Already Been Validated');
    }

    public function changeEmail(ChangeEmailRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->email = $request->email;
        $user->email_verified_at = null;
        $user->save();

        $this->sendToken($user);

        return $this->okResponse('Email Has Been Changed And Token Resent', new UserResource($request->user()));
    }

    protected function checkIfVerified($user): JsonResponse|bool
    {
        return is_null($user->email_verified_at);
    }

    protected function sendToken($user)
    {
        Mail::to($user)->queue(new VerifyEmail($user, new TotpService));
    }
}