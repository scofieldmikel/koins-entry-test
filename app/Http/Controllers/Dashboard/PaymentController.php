<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Http\Traits\PaymentTrait;
use App\Http\Controllers\Controller;
use App\Http\Traits\TransactionTrait;
use App\Http\Requests\FundCampaignRequest;
use App\Traits\Helpers;

class PaymentController extends Controller
{
    use TransactionTrait, PaymentTrait, Helpers;

    public function fundCampaign(Campaign $campaign, FundCampaignRequest $request)
    {
        $user = $request->user();
        if($campaign->status != $this->fetchStatusId('Pending')) {
            return $this->badRequestResponse('Campaign can not be funded.');
        }

        $transaction = $this->saveTransaction($campaign->amount, $user, $campaign, 'Transfer', 'Campaign Fund', $campaign->id, 'Campaign Fund');

        $data = [
            'event_type' => 'fund.campaign',
            'data' => [
                'campaign' => $campaign
            ],
        ];
        return $this->paymentMethod($request->payment_type, $transaction, $user, $data);
    }
}
