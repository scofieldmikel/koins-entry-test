<?php

namespace App\Models;

use App\Casts\KoboNaira;
use App\Helpers\Reference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    const SUCCESSFUL = 1;

    const PENDING = 0;

    const FAILED = 2;

    const APPROVED = 3;

    const ABANDON = 4;

    const CANCELLED = 5;

    const PAYSTACK = 'Paystack';

    const WALLET = 'Wallet';

    const CARD = 'Card';

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => KoboNaira::class,
        'data' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($transaction) {
            $transaction->reference = strtolower(Reference::getHashedToken());
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getStatus(): string
    {
        return match ((int) $this->status) {
            Transaction::SUCCESSFUL => 'Successful',
            Transaction::APPROVED => 'Approved',
            Transaction::ABANDON => 'Abandoned',
            Transaction::FAILED => 'Failed',
            Transaction::CANCELLED => 'Cancelled',
            default => 'Pending'
        };
    }
}
