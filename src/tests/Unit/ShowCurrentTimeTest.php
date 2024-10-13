<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

/**
 * @method artisan(string $string)
 */
class ShowCurrentTimeTest extends TestCase
{
    public function test_command_displays_current_time()
    {
        $mockedTime = '2024-10-10 10:00:00';
        Carbon::setTestNow($mockedTime);

        $this->artisan('time:current')
            ->expectsOutput('Current time: ' . $mockedTime)
            ->assertExitCode(0);
    }
}
