<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Campaign;
use App\Mail\SendCampaignMail;
use App\Traits\Helpers;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCampaign extends Command
{
    use Helpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send campaign notifications to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaigns = Campaign::where('status', $this->fetchStatusId('Running'))
            ->get();

        $bar = $this->output->createProgressBar($campaigns->count());

        $campaigns->lazy()->each(function (Campaign $campaign) use ($bar) {

            $externalEmails = [
                'test1@example.com',
                'test2@example.com',
                'hello@external.org',
                'info@randommail.net',
                'someone@example.org',
                'feedback@mailtester.com',
                'contact@demo.io',
            ];

            foreach ($externalEmails as $email) {
                Mail::to($email)->queue(new SendCampaignMail($campaign));
            }

            $bar->advance();
        });

        $bar->finish();
    }
}
