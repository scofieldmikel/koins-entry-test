<?php

namespace App\Listeners;

use App\Services\TotpService;
use App\Events\UserRegistered;
use App\Mail\Auth\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVerificationEmail
{
    protected $totpService;

    /**
     * Create the event listener.
     */
    public function __construct(TotpService $totpService)
    {
        $this->totpService = $totpService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        Mail::to($event->user)->send(new VerifyEmail($event->user, $this->totpService));
    }
}
