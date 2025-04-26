<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Traits\ComputeTrait;
use App\Traits\Helpers;
use Illuminate\Console\Command;

class DebitWalletForCampaign extends Command
{
    use ComputeTrait, Helpers;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debit-wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debit wallet for campaign';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaigns = Campaign::where('status', $this->fetchStatusId('Running'))
            ->get();

        $bar = $this->output->createProgressBar($campaigns->count());

        $campaigns->lazy()->each(function (Campaign $campaign) use ($bar) {

            // Calculate the daily budget for the campaign

            $campaign->load('locations');
            $onLocations = $campaign->locations->where('pivot.status', 'on');

            $daily_budget = $this->calculateAmount($campaign->start_date, $campaign->end_date, $onLocations)['daily_budget'];

            if ($daily_budget <= 0) {
                $bar->advance();
                return;
            }

            if ($campaign->user->wallet->balance < $daily_budget) {

                $campaign->status = $this->fetchStatusId('Stopped');
                $campaign->save();

                $this->info('Not enough balance for campaign: ' . $campaign->name);
                return;
            }
            // Check if the user has enough balance
            $campaign->user->wallet()->updateOrCreate(
                ['user_id' => $campaign->user->id],
                [
                    'balance' => ($campaign->user->wallet->balance ?? 0) - $daily_budget,
                ]
            );

            $bar->advance();
        });

        $bar->finish();
    }
}
