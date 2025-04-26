<?php

namespace App\Http\Traits;

use App\Helpers\Misc;
use App\Models\Auth\Transaction;
use App\Models\Transaction\Review;
use App\Models\User;

trait checkTransactionTrait
{
    use HasApiResponse, TransactionTrait;

    public function transaction(User $user)
    {

    }
}
