<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AiJson implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($this->isJson($value)) {
            return json_decode($value, true);
        }

        // Check if it's a string wrapped with ```
        if (preg_match('/^```json\s+(.+)```$/s', $value, $matches)) {
            // Attempt to decode the inner JSON string
            $json = trim($matches[1]);
            if ($this->isJson($json)) {
                return json_decode($json, true);
            }
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }

    /**
     * Determine if the given string is valid JSON.
     *
     * @param  string  $string
     */
    protected function isJson($string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
