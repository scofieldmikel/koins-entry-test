<?php

namespace App\Http\Controllers\Webhooks\Traits;

use App\Helpers\Misc;
use App\Helpers\Reference;
use App\Models\Finance\Transaction;
use App\Models\SavingsType\SavingsPlan;
use App\Models\SavingsType\SavingsPlanSchedule;
use Exception;
use Illuminate\Support\Facades\DB;
use Mail;

trait PaymentWebhookTrait
{
    use PaymentTrait;
    
    /**
     * @throws \Throwable
     */
    protected function walletNotification(Transaction $transaction, SavingsPlanSchedule $repayment, SavingsPlan $plan)
    {

        if ($transaction->amount == $repayment->amount) {
            $this->processFullPayment($repayment, $plan, $transaction);
        }
    }

}
