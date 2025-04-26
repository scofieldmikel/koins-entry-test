<?php

namespace App\Http\Controllers\Finance;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    public function getTransactions()
    {
        $user = request()->user();

        $transactions = $user->transactions()->latest()->paginate(10);

        return TransactionResource::collection($transactions);
    }

    public function getSingleTransaction(Transaction $transaction)
    {
        Gate::authorize('viewTransaction', $transaction);

        return new TransactionResource($transaction);
    }
}
