<?php

namespace App\Console\Commands;

use App\Jobs\BlurHashImage;
use Illuminate\Console\Command;

class BlurImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blur:image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        BlurHashImage::dispatch('d', 'd');
    }
}
