<?php

namespace App\Services\Sms\Repository;

interface SmsRepositoryInterface
{
    public function from($from);

    public function to($to);

    public function message($message);

    public function sendSms();
}
