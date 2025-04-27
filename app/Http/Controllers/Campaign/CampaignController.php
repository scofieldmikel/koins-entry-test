<?php

namespace App\Http\Controllers\Campaign;

use App\Models\Image;
use App\Helpers\Images;
use App\Jobs\MoveImage;
use App\Traits\Helpers;
use App\Models\Campaign;
use Illuminate\Support\Arr;
use App\Traits\ComputeTrait;
use Illuminate\Http\Request;
use App\Mail\CampaignCreated;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\CampaignResource;
use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\ModifyCampaignRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CampaignController extends Controller
{
    use Helpers, ComputeTrait;

    public function createCampaign(CreateCampaignRequest $request)
    {
        $existing = Campaign::where('owner_id', $request->user()->id)
                    ->where('name', $request->name)
                    ->exists();

        if ($existing) {
            return $this->badRequestResponse('You already have a campaign with this name.');
        }

        $total_budget = $this->calculateAmount($request->start_date, $request->end_date, $request->locations)['total_budget'];

        if ($total_budget > $request->amount) {
            return $this->badRequestResponse('Total budget exceeds the amount allocated for the campaign, your total budget is ' . $total_budget);
        }

        DB::beginTransaction();

        try {
            $campaign = Campaign::create([
                'owner_id' => $request->user()->id,
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'amount' => $request->amount,
                'status' => CampaignController::fetchStatusId('Pending'),
            ]);

            collect($request->locations)->each(function ($location) use ($campaign) {
                $campaign->locations()->attach($location['id'], ['status' => $location['status']]);
            });

            if ($request->filled('images')) {
                foreach ($request->images as $key => $path) {
                    $image = new Image([
                        'path' => Images::replaceTmpImage($path),
                        'note' => $key,
                    ]);
                    $campaign->images()->save($image);
                }

                dispatch(new MoveImage($request->user(), Arr::flatten($request->images)));
            }

            Mail::to($request->user())->queue(new CampaignCreated($request->user(), $campaign));

            DB::commit();

            return new CampaignResource($campaign->load(['locations', 'images', 'user']));

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            // Check if it's not a 500-level error
            $code = method_exists($e, 'getCode') ? $e->getCode() : 500;
            $message = $e->getMessage();

            if ($code && $code < 500) {
                return $this->badRequestResponse($message);
            }
            return $this->serverErrorResponse('Something went wrong while creating campaign.');
        }
    }

    public function getCampaigns(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->badRequestResponse('User not found');
        }

        $campaigns = $user->campaigns()
            ->with(['locations', 'images'])
            ->paginate(10);

        return CampaignResource::collection($campaigns);
    }

    public function getCampaignDetails($id)
    {
        try {
            $campaign = Campaign::with(['locations', 'images'])->findOrFail($id);
            Gate::authorize('viewCampaign', $campaign);
            return new CampaignResource($campaign);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Campaign not found');
        }
    }

    public function updateStatus(Request $request, $campaign)
    {
        $campaign = Campaign::find($campaign);

        if (!$campaign) {
            return $this->notFoundResponse('Campaign not found');
        }

        if(! $campaign->user->is($request->user())) {
            return $this->badRequestResponse('You are not authorized to add location to this campaign.');
        }

        if($campaign->status == CampaignController::fetchStatusId('Pending')) {
            return $this->badRequestResponse('Campaign is still in pending! Kindly fund campaign.');
        }
        if($campaign->end_date < now()) {
            return $this->badRequestResponse('Campaign has already ended.');
        }

        $request->validate([
            'status' => 'required|in:Pending,Stopped,Paused',
        ]);

        $newStatusId = CampaignController::fetchStatusId($request->status);

        if ($campaign->status == $newStatusId) {
            return $this->badRequestResponse('Campaign already has this status.');
        }

        $campaign->update([
            'status' => $newStatusId,
        ]);

        return $this->okResponse(
            'Campaign status updated successfully.',
            new CampaignResource($campaign->load(['locations', 'images']))
        );
    }

    public function modifyLocation($campaign, ModifyCampaignRequest $request)
    {
        $campaign = Campaign::find($campaign);

        if (!$campaign) {
            return $this->notFoundResponse('Campaign not found');
        }

        if(! $campaign->user->is($request->user())) {
            return $this->badRequestResponse('You are not authorized to add location to this campaign.');
        }

        $attached = $campaign->locations()->where('locations.id', $request->location_id)->exists();

        if (! $attached) {
            return $this->badRequestResponse('This location is not associated with the campaign.');
        }

        $campaign->locations()->updateExistingPivot($request->location_id, [
            'status' => $request->status,
        ]);

        return $this->okResponse('Location status updated successfully');
    }

    public function addLocationToExistingCampaign($campaign, ModifyCampaignRequest $request)
    {
        $campaign = Campaign::find($campaign);

        if (!$campaign) {
            return $this->notFoundResponse('Campaign not found');
        }

        if(! $campaign->user->is($request->user())) {
            return $this->badRequestResponse('You are not authorized to add location to this campaign.');
        }

        $campaign->locations()->syncWithoutDetaching([
            $request->location_id => ['status' => $request->status],
        ]);

        return $this->okResponse('Location added successfully');
    }

    public function resumeCampaign($campaign, Request $request)
    {
        $campaign = Campaign::find($campaign);

        if (!$campaign) {
            return $this->notFoundResponse('Campaign not found');
        }

        if(! $campaign->user->is($request->user())) {
            return $this->badRequestResponse('You are not authorized to add location to this campaign.');
        }

        if ($campaign->status == CampaignController::fetchStatusId('Pending')) {
            return $this->badRequestResponse('Campaign is still in pending! Kindly fund campaign.');
        }

        if ($campaign->status == CampaignController::fetchStatusId('Running')) {
            return $this->badRequestResponse('Campaign is already running, you cannot update the status.');
        }

        if($campaign->end_date < now()) {
            return $this->badRequestResponse('Campaign has already ended.');
        }

        if($campaign->status == CampaignController::fetchStatusId('Stopped') || $campaign->status == CampaignController::fetchStatusId('Paused')) {
            $campaign->update([
                'status' => CampaignController::fetchStatusId('Running'),
            ]);

            return $this->okResponse(
                'Campaign status updated successfully.',
                new CampaignResource($campaign->load(['locations', 'images']))
            );
        }

        return $this->badRequestResponse('Campaign is not in a state that can be resumed.');
    }

}
