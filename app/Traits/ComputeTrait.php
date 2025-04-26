<?php

namespace App\Traits;

trait ComputeTrait
{
    public function calculateAmount($start_date, $end_date, $locations)
    {
        $daily_rate = 2; // Daily rate for each location

        // Validate the dates
        if (!\Carbon\Carbon::hasFormat($start_date, 'Y-m-d') || !\Carbon\Carbon::hasFormat($end_date, 'Y-m-d')) {
            throw new \InvalidArgumentException('Invalid date format. Expected format: Y-m-d');
        }

        $start = \Carbon\Carbon::parse($start_date);
        $end = \Carbon\Carbon::parse($end_date);

        // Calculate the number of days between the start and end dates
        if ($start->greaterThan($end)) {
            throw new \InvalidArgumentException('Start date must be before end date.');
        }

        $days = $start->diffInDays($end);

        $locations = is_object($locations) && method_exists($locations, 'locations')
        ? $locations->locations
        : collect($locations);

        // Filter locations to count only those with status 'on'
        $onCount = collect($locations)->where('pivot.status', 'on')->count();

            $total_budget = $onCount * $days * $daily_rate;
            $daily_budget = $daily_rate * $onCount;

            return [
                'total_budget' => $total_budget,
                'daily_budget' => $daily_budget,
            ];
    }

}
