<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Paystack\Paystack;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PaystackValidateCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private User $user;

    private string $accountNumber;

    private string $bankCode;

    private string $bvn;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $accountNumber, $bankCode, $bvn)
    {
        $this->user = $user;
        $this->accountNumber = $accountNumber;
        $this->bankCode = $bankCode;
        $this->bvn = $bvn;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->user->paystack_customer()->exists()) {
            return;
        }
        if ($this->user->paystack_customer->identified) {
            return;
        }

        if (! isset($this->accountNumber)) {
            return;
        }

        if (! isset($this->bankCode)) {
            return;
        }

        if (! isset($this->bvn)) {
            return;
        }

        if (is_null($this->user->paystack_customer->customer_code)) {
            return;
        }

        Paystack::add('country', 'NG')
            ->add('type', 'bank_account')
            ->add('account_number', $this->accountNumber)
            ->add('bvn', $this->bvn)
            ->add('bank_code', $this->bankCode)
            ->add('first_name', $this->user->first_name)
            ->add('last_name', $this->user->last_name)
            ->validateCustomer($this->user->paystack_customer->customer_code);
    }
}
