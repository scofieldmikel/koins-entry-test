<?php

namespace App\Casts;

use DateTime;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Dob implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        $date = DateTime::createFromFormat('Y-m-d', $value);

        if ($date === false) {
            return null;
        }

        return $date->format('d-m-Y');
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        $date = DateTime::createFromFormat('d-m-Y', $value);

        return $date->format('Y-m-d');
    }
}
