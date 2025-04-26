<?php

namespace App\Services\Sms;

use App\Services\ServicesManager;

class SmsManager extends ServicesManager
{
    use SmsDriver;

    protected function getDefaultDriver()
    {
        return config('services.sms.default');
    }
}
