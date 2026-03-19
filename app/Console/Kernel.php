<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('events:send-reminders')->hourly();
        $schedule->command('events:check-deadlines')->everyThreeHours();
        $schedule->command('events:update-statuses')->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
