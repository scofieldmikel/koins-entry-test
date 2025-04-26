<?php

namespace App\Services\Notifications;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

class DatabaseChannel
{
    /**
     * Send the given notification.
     */
    public function send(mixed $notifiable, Notification $notification): Model
    {
        return $notifiable->routeNotificationFor('database', $notification)->create(
            [
                'id' => $notification->id,
                'type' => $this->getType($notification),
                'type_class' => get_class($notification),
                'data' => $notification->toArray($notifiable),
                'models' => json_encode($notification->models()),
                'read_at' => null,
            ]
        );
    }

    protected function getType(Notification $notification): string
    {
        return (new ReflectionClass($notification))->getShortName();
    }
}
