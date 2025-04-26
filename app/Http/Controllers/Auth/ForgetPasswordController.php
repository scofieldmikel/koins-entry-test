<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\PasswordChanged;
use App\Services\TotpService;
use App\Mail\ResetPasswordEmail;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyPasswordResetRequest;

class ForgetPasswordController extends Controller
{
    protected TotpService $totpService;

    /**
     * ForgotPasswordController constructor.
     */
    public function __construct(TotpService $totpService)
    {
        $this->totpService = $totpService;
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if ($user !== null) {
            Mail::to($user)->queue(new ResetPasswordEmail($user, $this->totpService));
        }

        return $this->okResponse('An email will be sent to you if we can find your email.');
    }

    public function changePassword(VerifyPasswordResetRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        Mail::to($user)->queue(new PasswordChanged($user));

        return $this->okResponse('Password Saved Successfully');
    }
}
