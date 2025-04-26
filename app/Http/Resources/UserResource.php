<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $status;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  bool  $status
     */
    public function __construct($resource, bool $status = false)
    {
        parent::__construct($resource);

        $this->status = $status;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'profile_image' => $this->images(),
            'verified' => ! is_null($this->email_verified_at),
            'verified_phone' => ! is_null($this->phone_verified_at),
            $this->mergeWhen($this->status, [
                'token' => $this->createToken('API Token')->plainTextToken,
            ]),
        ];
    }

    public function images($status = false): string
    {
        $disk = 's3'; // explicitly use the s3 disk

        if ($status) {
            return is_null($this->profile_image)
                ? 'http://www.gravatar.com/avatar/' . md5($this->email) . '?s=150'
                : Storage::disk($disk)->url($this->profile_image);
        }

        if (!is_null($this->profile_image)) {
            return Storage::disk($disk)->url($this->profile_image);
        }

        return 'http://www.gravatar.com/avatar/' . md5($this->email) . '?s=150';
    }
}
