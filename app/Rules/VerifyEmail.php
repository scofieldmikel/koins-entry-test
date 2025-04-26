<?php

namespace App\Rules;

use App\Services\Emailable\Emailable;
use Illuminate\Contracts\Validation\Rule;

class VerifyEmail implements Rule
{
    public string $error = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $result = Emailable::add('email', $value)->verify();

        if (! isset($result['reason'])) {
            $this->error = 'Request takes too long! Try again later.';

            return false;
        }

        if (($result['reason'] === 'rejected_email') || ($result['reason'] === 'invalid_email') || ($result['reason'] === 'invalid_domain')) {
            $explodedError = explode('_', $result['reason']);

            $this->error = ucwords($explodedError[0]).' '.ucwords($explodedError[1]);

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->error;
    }
}
