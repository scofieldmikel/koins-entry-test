<?php

namespace App\Http\Resources;

use App\Traits\Helpers;
use App\Traits\ComputeTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    use Helpers, ComputeTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'amount' => $this->amount,
            'status' => $this->fetchStatusName($this->status),
            'locations' => LocationResource::collection($this->whenLoaded('locations')),
            'banners' => ImageResource::collection($this->whenLoaded('images')),
            'owner' => new UserResource($this->user),
            'total_budget' => $this->calculateAmount($this->start_date, $this->end_date, $this->locations)['total_budget'],
            'daily_budget' => $this->calculateAmount($this->start_date, $this->end_date, $this->locations)['daily_budget'],
        ];
    }
}
