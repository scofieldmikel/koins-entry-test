<?php

namespace App\Console;

use App\Console\Commands\ChangeStatus;
use App\Console\Commands\SendCampaign;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\DebitWalletForCampaign;
use App\Console\Commands\NotifyCampaignCompletion;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        NotifyCampaignCompletion::class,
        ChangeStatus::class,
        SendCampaign::class,
        DebitWalletForCampaign::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('campaign-completion')->daily();
        $schedule->command('change-status')->daily();
        $schedule->command('send-campaign')->daily();
        $schedule->command('debit-wallet')->daily('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
