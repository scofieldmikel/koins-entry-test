<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Facade;

/**
 * @method static disk(string $string)
 */
class NotifySms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'sms';
    }
}
