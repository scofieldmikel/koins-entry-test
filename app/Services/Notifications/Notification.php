<?php

namespace App\Services\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification as BaseNotification;
use ReflectionClass;
use ReflectionException;

class Notification extends BaseNotification
{
    public function models(): array
    {
        $reflection = new ReflectionClass($this);

        $params = $reflection->getConstructor()->getParameters();

        return array_map(/**
         * @throws ReflectionException
         */ function ($param) {
            $class = $param->getType() && ! $param->getType()->isBuiltin()
               ? new ReflectionClass($param->getType()->getName())
                  : null;

            if ($class === null) {
                return;
            }
            if (! $class->isSubclassOf(Model::class)) {
                return;
            }

            return [
                'id' => $this->{$param->name}->id,
                'class' => $class->name,
            ];
        }, $params);
    }
}
