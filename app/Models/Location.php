<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
    ];
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_location')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}
