<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Traits\Helpers;
use App\Models\Campaign;
use App\Mail\CampaignCompleted;
use Illuminate\Console\Command;
use App\Mail\CampaignToComplete;
use Illuminate\Support\Facades\Mail;

class NotifyCampaignCompletion extends Command
{
    use Helpers;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign-completion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users of campaign completion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaigns = Campaign::where('status', $this->fetchStatusId('Running'))
            ->get();

        $bar = $this->output->createProgressBar($campaigns->count());

        $campaigns->lazy()->each(function (Campaign $campaign) use ($bar) {

            $end_date = now()->startOfDay()->diffInDays(Carbon::parse($campaign->end_date)->copy()->startOfDay(), false);

            $user = $campaign->user;

            if ($end_date == 2) {
                Mail::to($user)->queue(new CampaignToComplete($user, $campaign));
            }

            if ($end_date == 0) {
                Mail::to($user)->queue(new CampaignCompleted($user, $campaign));
            }

            $bar->advance();
        });

        $bar->finish();
    }
}
