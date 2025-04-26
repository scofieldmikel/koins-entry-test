<?php

namespace App\Services\Emailable;

use App\Http\Traits\HasApiResponse;
use App\Services\Contract\HttpTrait;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Config
{
    use HasApiResponse, HttpTrait, Verify;

    protected string $secretKey;

    protected PendingRequest $client;

    protected string $baseUrl = 'https://api.emailable.com/v1/';

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->setConstant();
        $this->checkConstant();
        $this->prepareClient();
        $this->prepareUserAgent();
    }

    protected function setConstant(): void
    {
        $this->secretKey = config('services.emailable.secretkey');
    }

    /**
     * @throws Exception
     */
    protected function checkConstant()
    {
        if (empty($this->secretKey)) {
            throw new Exception('Keys Not Set');
        }
    }

    protected function prepareClient()
    {
        $this->client = Http::withToken($this->secretKey)->acceptJson();
    }
}
