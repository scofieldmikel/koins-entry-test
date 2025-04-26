<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Http\Traits\PaymentTrait;
use App\Http\Controllers\Controller;
use App\Http\Traits\TransactionTrait;
use App\Http\Requests\FundCampaignRequest;

class PaymentController extends Controller
{
    use TransactionTrait,PaymentTrait;

    public function fundCampaign(Campaign $campaign, FundCampaignRequest $request)
    {
        $user = $request->user();
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
