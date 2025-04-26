<?php

namespace App\Services\Sms;

use App\Services\Sms\Repository\SmsRepository;

class Termii extends SmsRepository
{
    protected \App\Services\Termii\Termii $gateway;

    public function __construct(\App\Services\Termii\Termii $gateway)
    {
        $this->gateway = $gateway;
    }

    public function sendSms()
    {
        return $this->gateway->add('to', $this->to)->add('sms', $this->message)->add('type', 'plain')->add('channel', 'dnd')->send();
    }
}
