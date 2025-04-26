<?php

namespace App\Http\Controllers\Webhooks\Traits;


use Exception;
use App\Models\User;
use App\Helpers\Reference;
use Illuminate\Http\JsonResponse;
use App\Models\Finance\Withdrawal;
use Illuminate\Support\Facades\DB;
use App\Models\Finance\Transaction;
use App\Services\Paystack\Paystack;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\SavingsType\SavingsPlan;
use App\Models\Paystack as AuthPaystack;
use App\Models\SavingsType\SavingsPlanSchedule;
use App\Http\Controllers\Webhooks\WalletWebhook;
use App\Http\Traits\HasApiResponse;
use App\Models\Invest\LiquidatedInvestment;
use App\Models\Invest\UserInvestment;

trait PaymentTrait
{
    use HasApiResponse;

    protected array $url = [
        'local' => 'http://lily_backend.test',
        'staging' => 'https://lily.com',
        'testing' => 'http://lily.test',
        'production' => 'https://lily.com',
    ];

    protected  function updateSavingPlan($plan, $amount){
        $savings_plan = $plan;
        $savings_plan->current_amount += $amount;
        $savings_plan->save();
    }
    /**
     * @throws \Throwable
     */
    protected function lockForUpdate( User $user, $amount)
    {
        // Locking based on the user's ID to avoid race conditions on the wallet
        Cache::lock($user->id . '_get_wallet', 10)->get(function () use ($user, $amount) {
            $wallet = $user->wallet()->first();
            if (! $wallet || $wallet->wallet_balance < $amount) {
                throw new Exception('insufficient amount', 422, $amount);
            }
        });
    }

    protected function paymentMethod($payment_type, Transaction $transaction, User $user, $data): JsonResponse
    {
        return match ($payment_type) {
            'paystack' => $this->withPaystack($transaction, $user, $data, ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer']),
            'charge' => $this->withCardCharge($transaction, $user, $data),
            'bank' => $this->withPaystack($transaction, $user, $data, ['bank']),
            'wallet' => $this->withWallet($transaction, $user, $data),
            'withdrawal'=>$this->saveWalletWithdrawal($transaction,$user,$data),
            'liquidate'=>$this->InvestmentLiquidation($transaction,$user,$data),
            default => $this->withPaystack($transaction, $user, $data, ['card'])
        };
    }

    /**
     * @throws \Throwable
     */
    protected function withWallet(Transaction $transaction, User $user, $data)
    {
        $currency = $data['currency'];
        $balanceField = $currency.'_balance';

        $wallet = $user->wallet()->first();
        if (!Schema::hasColumn('wallets', $balanceField)) {
            return $this->badRequestResponse('Invalid currency balance field');
        }
        $balance = $wallet->$balanceField;

        // Check if the transaction amount is greater than the balance
        if ($transaction->amount > $balance) {
            return $this->badRequestResponse('Insufficient Balance, Kindly Topup Your Wallet');
        }

        (new WalletWebhook($user, $transaction))->handle($data, $transaction->amount);

        return $this->okResponse('Paid Successfully', [
            'mode' => 'wallet',
            'reference' => $transaction->reference,
        ]);
    }

    protected function withPaystack(Transaction $transaction, User $user, $data, $channel): JsonResponse
    {
        $this->saveChannel($transaction);

        $result = $this->processPaystack($transaction, $user, $data, $channel);

        return $this->okResponse('Authorization URL created', $result['data']);
    }

    protected function withCardCharge(Transaction $transaction, User $user, $data)
    {
        $payStack = $user->paystack()->where('paying', true)->first();

        if (is_null($payStack)) {
            return $this->badRequestResponse('You have not set a default card');
        }

        $this->saveChannel($transaction);

        $result = $this->processPaystackCharge($transaction, $payStack, $data);

        if (isset($result['data']['status']) && $result['data']['status'] === 'success') {
            return $this->okResponse('Charge attempted', [
                'mode' => 'Charge attempted',
                'reference' => $transaction->reference,
            ]);
        } else {
            return $this->badRequestResponse('We were unable to charge your account');
        }
    }
    protected function processPaystack(Transaction $transaction, User $user, array $data = [], $channel = ['card'])
    {
        $payment = Paystack::add('amount', (int) round($transaction->amount * 100))
            ->add('email', $user->email)
            ->add('currency', 'NGN')
            ->add('channels', $channel)
            ->add('metadata', $data)
            ->add('reference', $transaction->reference)
            ->add('callback_url', $this->url[config('app.env')]);
        return $payment->initialize();
    }

    protected function processPaystackCharge(Transaction $transaction, AuthPaystack $payStack, $data = [])
    {
        $payment = Paystack::add('amount', $transaction->amount * 100)
            ->add('email', $payStack['customer']['email'])
            ->add('authorization_code', $payStack->authorization['authorization_code'])
            ->add('metadata', $data)
            ->add('reference', $transaction->reference);

        if (! empty($data['plan'])) {
            $payment->add('plan', $data['plan']);
        }

        return $payment->authorization();
    }

    protected function saveWalletWithdrawal($transaction, $user, $data)
    {
        DB::beginTransaction();
        try {

            $amount = $transaction->amount;

            // Locking based on the user's ID to avoid race conditions on the wallet
            $this->lockForUpdate($user, $amount);

            // Remove from the plan wallet
            $plan = $data['data']['savings_plan'];
            $plan->current_amount -= $transaction->amount;
            $plan->save();
            $balanceField = $data['data']['currency'] . '_balance';

            // Update the user wallet balance
            $user_wallet = $user->wallet()->first();
            $user_wallet->$balanceField += $transaction->amount;
            $user_wallet->save();

            Withdrawal::where('reference', $transaction->reference)->update(['status' => 1]);

            // Update transaction status to successful
            $transaction->status = Transaction::SUCCESSFUL;
            $transaction->save();
            DB::commit();
            return $this->okResponse('Withdrawn Successfully', [
                'mode' => 'wallet',
               'reference' => $transaction->reference,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            \Log::error('Error in saveWalletWithdrawal: ' . $e->getMessage());

            // Re-throw the exception to handle it in the calling method
            throw $e;
        }
    }

    /**
     * @throws \Throwable
     */
    protected function InvestmentLiquidation($transaction, $user, $data)
    {
        DB::beginTransaction();
        try {
            $amount = $transaction->amount;

            // Locking based on the user's ID to avoid race conditions on the wallet
            // $this->lockForUpdate($user, $amount);

            $userInvestment = $data['data']['invest'];
            $LiquidatedInvestment = $data['data']['liquidate_investment'];

            $balanceField = $data['data']['currency'] . '_balance';

            // Update the user wallet balance
            $user_wallet = $user->wallet()->first();
            $user_wallet->$balanceField += $transaction->amount;
            $user_wallet->save();

            $userInvestment->update([
                'status' => UserInvestment::LIQUIDATED,
            ]);

            $LiquidatedInvestment->update([
                'liquidated_amount' => $transaction->amount,
                'status' => LiquidatedInvestment::COMPLETED,
            ]);

            $transaction->status = Transaction::SUCCESSFUL;
            $transaction->save();

            DB::commit();
            return $this->okResponse('Investment liquidated successfully.');

        } catch (Exception $e) {
            DB::rollback();
            report($e);
            throw $e;
        }
    }
}
