<?php

namespace App\Casts;

use App\Helpers\Misc;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MergeStepArray implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return is_null($value) ? $value : json_decode($value, true);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        $mergedData = Misc::mergeData($model->{$key}, $value);

        // Check for keys to unset
        $originalData = $model->{$key};
        if (is_array($originalData) || is_object($originalData)) {
            foreach ($originalData as $originalKey => $originalValue) {
                if (! isset($value[$originalKey])) {
                    unset($mergedData[$originalKey]);
                }
            }
        }

        return json_encode($mergedData);
    }
}
