<?php

namespace App\Http\Traits;

use App\Models\Transaction;

trait TransactionTrait
{
    public function saveTransaction($amount, $user, $campaign, $description, $action = 'Payment', $charges = 0, $currency = 'NGN', $status = 0): Transaction
    {
        $transaction = new Transaction([
            'description' => $description,
            'amount' => $amount,
            'action' => $action,
            'status' => $status,
            'currency' => $currency,
            'charges' => $charges,
            'campaign_id' => $campaign->id,
        ]);

        $user->transactions()->save($transaction);

        return $transaction;
    }

    /**
     * @throws \Throwable
     */
    protected function saveChannel(Transaction $transaction, $channel = Transaction::PAYSTACK)
    {
        $transaction->channel = $channel;
        $transaction->save();
    }

    public function calculateWithdrawalPaystackCharge($amount)
    {
        $additionalCharge = 100;
        if ($amount <= 5000) {
            return $additionalCharge + 10;
        }
        elseif ($amount >= 5001 && $amount <= 50000)
        {
            return $additionalCharge + 25;
        }
        else
        {
            return $additionalCharge + 50;
        }
    }

    public function getMethod($payment_type): string
    {
        return match ($payment_type) {
            'card' => Transaction::CARD,
            'paystack' => Transaction::PAYSTACK,
            'wallet' => Transaction::WALLET ,
            default => Transaction::CARD
        };
    }
}
