<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        // $schedule->command('users:renewInfo', [1])->daily()->when(function () {
        //     return now()->addMonths(11);
        // });
        // $schedule->command('users:renewInfo', [1])->monthly()->when(function () {
        //     return Carbon::now()->addMonths(11);
        // });
        // $schedule->command('users:renewInfo', [1])->when(function () {
        //     $date1 = Carbon::parse('2024-03-29 16:27:46');
        //     $date2 = Carbon::parse('2024-04-29 16:27:46');
        //     return $date1->diffInMonths($date2);
        // });

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
