<?php

namespace App\Services\Sms;

use App\Services\Sms\Repository\SmsRepository;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Log;

/**
 * @property Repository|Application|mixed apiKey
 * @property Repository|Application|mixed username
 */
class AfricaIsTalking extends SmsRepository
{
    protected $gateway;

    public function __construct($gateway)
    {
        $this->apiKey = config('service.sms.africaApiKey');
        $this->username = config('service.sms.Username');
        $this->gateway = $gateway;
    }

    public function sendSms()
    {
        try {
            $sms = $this->gateway->sms();
            $result = $sms->send([
                'from' => config('services.sms.from'),
                'to' => $this->to,
                'message' => $this->message,
            ]);
        } catch (HttpClientException $exception) {
            Log::info($exception->getMessage());
        }

        return $result;
    }
}
