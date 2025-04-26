<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Campaign extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'campaign_location')
                    ->withPivot('status')
                    ->withTimestamps();
    }
    public function banners()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function status()
    {
        return $this->belongsTo(CampaignStatus::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
