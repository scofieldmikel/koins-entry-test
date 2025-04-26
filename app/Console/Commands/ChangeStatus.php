<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Traits\Helpers;
use Illuminate\Console\Command;

class ChangeStatus extends Command
{
    use Helpers;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change status of campaigns from Running to Completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaigns = Campaign::where('status', $this->fetchStatusId('Running'))
            ->where('end_date', '<=', now())
            ->get();

        $bar = $this->output->createProgressBar($campaigns->count());

        $campaigns->lazy()->each(function (Campaign $campaign) use ($bar) {

            $campaign->status = $this->fetchStatusId('Completed');
            $campaign->save();

            $bar->advance();
        });

        $bar->finish();
    }
}
