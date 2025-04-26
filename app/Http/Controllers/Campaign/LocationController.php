<?php

namespace App\Http\Controllers\Campaign;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Http\Resources\LocationResource;

class LocationController extends Controller
{
    public function getLocations()
    {
        // Fetch locations with status 'active'
        $locations = Location::where('status', true)
            ->get();

        return LocationResource::collection($locations);
    }

    public function getLocationDetails($location)
    {
        $location = Location::find($location);

        if (! $location) {
            return $this->notFoundResponse('Location not found');
        }
        // Fetch location details
        return new LocationResource($location);
    }

    public function addLocation(LocationRequest $request)
    {
        // Create new location
        $location = Location::create($request->all());

        return new LocationResource($location);
    }

    public function updateLocation(Request $request, $location)
    {
        $location = Location::find($location);

        if (! $location) {
            return $this->notFoundResponse('Location not found');
        }

        // Update location details
        $location->update($request->all());

        return new LocationResource($location);
    }

}
