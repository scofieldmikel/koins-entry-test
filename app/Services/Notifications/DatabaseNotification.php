<?php

namespace App\Services\Notifications;

use App\Filters\Notification\NotificationFilter;
use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;
use Infinitypaul\LaravelDatabaseFilter\Traits\filterTrait;

/**
 * @mixin IdeHelperDatabaseNotification
 */
class DatabaseNotification extends BaseDatabaseNotification
{
    use filterTrait;

    protected string $filter = NotificationFilter::class;

    public function getModelsAttribute($value): array
    {
        return (array) json_decode($value);
    }

    public function scopeCounted(): int
    {
        return $this->whereNull('read_at')->where('type', 'UserNotify')->count();
    }
}
