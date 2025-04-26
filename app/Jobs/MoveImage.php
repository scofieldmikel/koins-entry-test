<?php

namespace App\Jobs;

use App\Models\User;
use App\Helpers\Images;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MoveImage implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected array $images;

    protected User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, array $images)
    {
        $this->images = $images;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        foreach ($this->images as $image_path) {
            Images::processPath($image_path, $this->user->id);
        }
    }
}
