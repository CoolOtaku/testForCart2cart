<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class ShowCurrentTime extends Command
{

    protected $signature = 'time:current';

    protected $description = 'Display the current time';

    public function handle()
    {
        $currentTime = Carbon::now()->toDateTimeString();

        $this->info('Current time: ' . $currentTime);
    }
}
