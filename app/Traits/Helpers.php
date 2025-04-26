<?php

namespace App\Traits;

use App\Models\CampaignStatus;
use App\Models\User;
use Exception;

trait Helpers
{

    public static function fetchStatusId(string $name): int
    {
        $status = CampaignStatus::where('name', $name)
            ->pluck('id')
            ->first();

            if (is_null($status) || empty($status)) {
                throw new \Exception('The selected status name could not be retrieved!');
            }

        return $status;
    }

    public static function fetchStatusName(int $statusId): string | Exception
    {
        $status = CampaignStatus::find($statusId);

        if (is_null($status)) {
            return new Exception('The selected status ID could not be retrieved!');
        }

        return $status->name;
    }
}
