<?php

namespace App\Services\Emailable;

use JetBrains\PhpStorm\Pure;

/**
 * @method static add(string $string, string $string1)
 * @method static verify(mixed $reference)
 */
class Emailable
{
    #[Pure]
    protected static function process(): Config
    {
        return new Config;
    }

    public static function __callStatic($method, $arguments)
    {
        return self::process()->{$method}(...$arguments);
    }
}
