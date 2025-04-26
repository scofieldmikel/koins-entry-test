<?php

namespace App\Services\Sms\Repository;

abstract class SmsRepository implements SmsRepositoryInterface
{
    protected string $to;

    protected string $from;

    protected string $message;

    public function to($to): static
    {
        $this->to = $to;

        return $this;
    }

    public function from($from): static
    {
        $this->from = $from;

        return $this;
    }

    public function message($message): static
    {
        $this->message = $message;

        return $this;
    }

    abstract public function sendSms();
}
