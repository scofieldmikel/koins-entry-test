<?php

namespace App\Services;

use App\Services\Sms\SmsDriver;
use Illuminate\Foundation\Application;
use InvalidArgumentException;

class ServicesManager
{
    use SmsDriver;

    protected Application $app;

    protected array $disk = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function disk($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->disk[$name] = $this->get($name);
    }

    protected function get($name)
    {
        return $this->disk[$name] ?? $this->resolve($name);
    }

    protected function resolve($name)
    {
        $config = $this->getConfig($name);
        if (is_null($config)) {
            throw new InvalidArgumentException("Disk [{$name}] Doesnt Exist ");
        }

        $driverMethod = 'create'.ucfirst($name).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }
        throw new InvalidArgumentException("Disk [{$name}] Is Not Supported ");
    }

    protected function getConfig($name)
    {
        return $name ?: $this->getDefaultDriver();
    }

    protected function getDefaultDriver()
    {
        return config('service.sms.default');
    }

    public function __call($name, $arguments)
    {
        return $this->disk()->$name(...$arguments);
    }
}
