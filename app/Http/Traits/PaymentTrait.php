<?php

namespace App\Http\Traits;

use Exception;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use App\Services\Paystack\Paystack;
use Illuminate\Support\Facades\Schema;
use App\Models\Paystack as AuthPaystack;

trait PaymentTrait
{
    use HasApiResponse;

    protected array $url = [
        'local' => 'http://koins_test_auth.test',
        'staging' => 'https://koins_test_auth.test',
        'testing' => 'http://koins_test_auth.test',
    ];

    /**
     * @throws \Throwable
     */
    protected function paymentMethod($payment_type, Transaction $transaction, User $user, $data): JsonResponse
    {
        return match ($payment_type) {
            'paystack' => $this->withPaystack($transaction, $user, $data, ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer']),
            'charge' => $this->withCardCharge($transaction, $user, $data),
            'bank' => $this->withPaystack($transaction, $user, $data, ['bank']),
            default => $this->withPaystack($transaction, $user, $data, ['card'])
        };
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
        return Paystack::add('amount', $transaction->amount * 100)
            ->add('email', $user->email)
            ->add('currency', 'NGN')
            ->add('channels', $channel)
            ->add('metadata', $data)
            ->add('reference', $transaction->reference)
            ->add('callback_url', $this->url[config('app.env')])
            ->initialize();
    }

    protected function processPaystackCharge(Transaction $transaction, AuthPaystack $payStack, $data = [])
    {
        return Paystack::add('amount', $transaction->amount * 100)
            ->add('email', $payStack['customer']['email'])
            ->add('authorization_code', $payStack->authorization['authorization_code'])
            ->add('metadata', $data)
            ->add('reference', $transaction->reference)
            ->authorization();
    }
}
