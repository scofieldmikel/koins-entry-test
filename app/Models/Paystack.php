<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paystack extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['log', 'authorization', 'customer'];

    protected $casts = [
        'customer' => 'array',
        'authorization' => 'array',
        'log' => 'array',
        'paying' => 'boolean',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
