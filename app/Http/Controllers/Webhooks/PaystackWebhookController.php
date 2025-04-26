<?php

namespace App\Http\Controllers\Webhooks;

use Exception;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\TransactionTrait;
use App\Http\Controllers\Webhooks\Traits\PaymentTrait;
use App\Models\Campaign;
use App\Traits\Helpers;

class PaystackWebhookController
{
    use PaymentTrait,TransactionTrait, Helpers;

    public function handle(Request $request)
    {
        Log::info(print_r($request->all(), true));
        $method = Str::replace('.', '', ucwords($request->event, '.'));

        if (method_exists($this, $handler = 'handle'.$method)) {
            $this->{$handler}($request->data);
        }
    }

    /**
     * @throws Exception
     */
    protected function checkTransaction($request): \Illuminate\Database\Eloquent\Builder|Transaction|null
    {
        $amount = $request['requested_amount'] ?? $request['amount'];

        $transaction = Transaction::where('reference', $request['reference'])
            ->where('amount', $amount)
            ->where('status', Transaction::PENDING)
            ->first();

        if (! $this->checkTransactionValid($request, $transaction)) {
            Log::info('Invalid Transaction');
            Log::info(print_r($request, true));
            throw new Exception('Invalid Transaction');
        }

        $this->createTransactionForPartial($request, $transaction);

        $transaction->status = true;
        $transaction->save();

        return $transaction;
    }

    protected function createTransactionForPartial($request, Transaction $transaction)
    {
        if (isset($request['requested_amount']) && $request['requested_amount'] > $request['amount']) {
            $transaction->amount = $request['amount'] / 100;
            $transaction->save();
        }
    }


    /**
     * @throws Exception
     * @throws \Throwable
     */
    protected function handleChargeSuccess($request)
    {
        $transaction = $this->checkTransaction($request);

        $method = Str::replace('.', '', ucwords($request['metadata']['event_type'], '.'));

        if (method_exists($this, $handler = 'handled'.$method)) {
            $this->{$handler}($request, $transaction);
        }
    }

    protected function checkTransactionValid($request, $transaction): bool
    {
        return $request['status'] === 'success' && ! is_null($transaction);
    }

    protected function handledAuthorize($request, Transaction $transaction)
    {
        if ($request['authorization']['authorization_code']) {
            $transaction->user->paystack()->updateOrCreate(
                ['user_id' => $transaction->user->id,
                    'authorization->last4' => $request['authorization']['last4'],
                    'customer->customer_code' => $request['customer']['customer_code'],
                ],
                [
                    'log' => $request['log'],
                    'authorization' => $request['authorization'],
                    'customer' => $request['customer'],
                ]
            );
        }
    }

    /**
     * @throws Exception
     */
    protected function handleTransferSuccess($request)
    {
        $transactionLog = $this->checkTransaction($request);

        if (Str::contains($transactionLog->reference, 'Wdw_')) {
            // $this->processWithdrawal($request, $transactionLog);

            return;
        }
    }

    /**
     * @throws Exception
     */

    protected function handledFundCampaign($request)
    {
        $data = $request['authorization'];
        unset($data['authorization_code']);

        $campaign = Campaign::whereId($request['metadata']['data']['campaign'])->first();

        if ($campaign->status == $this->fetchStatusId('Pending')) {
            $campaign->status = $this->fetchStatusId('Running');
        }
        $campaign->save();

    }
}
