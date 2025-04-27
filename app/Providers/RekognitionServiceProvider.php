<?php

namespace App\Providers;

use App\Services\Rekognition\Rekognition;
use App\Services\Rekognition\RekognitionContract;
use Illuminate\Support\ServiceProvider;

class RekognitionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(RekognitionContract::class, function ($app) {
            $client = new \Aws\Rekognition\RekognitionClient([
                'region' => 'us-east-1',
                'version' => 'latest',
                'credentials' => [
                    'key' => config('services.rekognition.access_key'),
                    'secret' => config('services.rekognition.secret_key'),
                ],
            ]);

            return new Rekognition($client);
        });
    }
}
