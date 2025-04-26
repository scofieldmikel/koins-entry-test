<?php

namespace App\Services\Emailable;

use Illuminate\Http\Client\RequestException;

trait Verify
{
    /**
     * @throws RequestException
     */
    public function verify()
    {
        if (config('services.emailable.mode') === 'staging' || config('services.emailable.mode') === 'local') {
            $this->body['email'] = 'deliverable@example.com';
        }
        $this->get('verify');

        return $this->response;
    }
}
