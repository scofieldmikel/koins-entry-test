<?php

namespace App\Http\Controllers\Campaign;

use Illuminate\Http\Request;
use App\Models\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use App\Http\Resources\StatusResource;
use Illuminate\Database\Console\Migrations\StatusCommand;

class StatusController extends Controller
{
    public function addCampaignStatus(StatusRequest $request)
    {
        $campaignStatus = CampaignStatus::create([
            'name' => $request->name,
            'is_active' => $request->is_active,
            'is_visible' => $request->is_visible,
        ]);

        return $this->okResponse('Campaign Status Created Successfully', new StatusResource($campaignStatus));
    }

    public function getCampaignStatus()
    {
        $statuses = CampaignStatus::all();
        return $this->okResponse('Campaign Status Fetched Successfully', StatusResource::collection($statuses));
    }

    public function getCampaignStatusDetails($status)
    {
        $status = CampaignStatus::find($status);

        if (!$status) {
            return $this->notFoundResponse('Campaign Status not found');
        }

        return $this->okResponse('Campaign Status Fetched Successfully', new StatusResource($status));
    }

    public function updateCampaignStatus(StatusRequest $request, $status)
    {
        $status = CampaignStatus::find($status);

        if (!$status) {
            return $this->notFoundResponse('Campaign Status not found');
        }

        $data = [];

        if (!is_null($request->name) && $request->name !== $status->name) {
            // Check if the name already exists in another record
            $exists = CampaignStatus::where('name', $request->name)
                ->where('id', '!=', $status->id)
                ->exists();

            if ($exists) {
                return $this->badRequestResponse('The name already exists.');
            }

            $data['name'] = $request->name;
        }

        if (!is_null($request->is_active)) {
            $data['is_active'] = $request->is_active;
        }

        if (!is_null($request->is_visible)) {
            $data['is_visible'] = $request->is_visible;
        }

        if (!empty($data)) {
            $status->update($data);
        }

        return $this->okResponse('Campaign Status Updated Successfully', new StatusResource($status));
    }
}
