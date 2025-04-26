<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class KoboNaira implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): float|int
    {
        return round($value / 100, 2);
    }

    public function set($model, string $key, $value, array $attributes): float|int
    {
        return (int) round($value * 100);
    }
}
