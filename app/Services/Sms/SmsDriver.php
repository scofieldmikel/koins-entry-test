<?php

namespace App\Services\Sms;

use AfricasTalking\SDK\AfricasTalking;
use JetBrains\PhpStorm\Pure;

trait SmsDriver
{
    //    public function createAfricasTalkingDriver(): AfricaIsTalking
    //    {
    ////        $gateway = new AfricasTalking(config("services.sms.africaUsername"), config("services.sms.africaApiKey"));
    ////        return new AfricaIsTalking($gateway);
    //        return;
    //    }

    #[Pure]
    public function createTermiiDriver(): Termii
    {
        $gateway = new \App\Services\Termii\Termii;

        return new Termii($gateway);
    }
}
