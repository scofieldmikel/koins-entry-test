<?php

namespace App\Http\Controllers\Webhooks;


use Exception;
use App\Models\User;
use App\Models\Saving;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\TransactionTrait;
use App\Http\Controllers\Webhooks\Traits\PaymentTrait;
use App\Http\Controllers\Webhooks\Traits\PaymentWebhookTrait;

class WalletWebhook
{
    use PaymentTrait, TransactionTrait,PaymentWebhookTrait;

    public $user;
    public $transaction;

    public function __construct(
        User $user,
        Transaction $transaction
    ) {
        $this->transaction = $transaction;
        $this->user = $user;
    }

    /**
     * @throws \Throwable
     */
    public function handle($data, $amount)
    {
        $method = Str::replace('.', '', ucwords($data['event_type'], '.'));

        $this->processWalletDeduction($amount,$data);

        if (method_exists($this, $handler = 'handle'.$method)) {
            $this->{$handler}($data, $amount);
        }
    }

    /**
     * @throws \Throwable
     */
    protected function processWalletDeduction($amount,$data)
    {
        DB::beginTransaction();
        try {
            $currency = $data['data']['currency'];
            $balanceField = $currency.'_balance';
            $user_wallet = $this->user->wallet()->first();
            $balance = $user_wallet->$balanceField;

            if ($amount > $balance) {
                $whatIsLeft = $balance;
                $newBalance = 0;
            } else {
                $newBalance = $balance - $amount;
                $whatIsLeft = $amount;
            }
            $this->transaction->amount = $whatIsLeft;
            $this->transaction->channel = Transaction::WALLET;
            $this->transaction->status = Transaction::SUCCESSFUL;
            $this->transaction->save();

            $user_wallet->$balanceField = $newBalance;
            $user_wallet->save();

            DB::commit();
        } catch (Exception $e) {
            Log::info(print_r($e->getMessage(), true));
            DB::rollback();
        }
    }

    protected function handleCampaignPlan($data, $amount)
    {
        // $currency = $data['data']['currency'];
        // $savings_plan = $data['data']['savings_plan'];
        // $savings_plan->current_amount += $amount;
        // if ($savings_plan->status === 'pending') {
        //     $savings_plan->status = "ongoing";
        //     $savings_plan->currency = $currency;
        // }
        // $savings_plan->save();
        // $savings_plan->transactions()->attach($this->transaction->id);

    }

}
