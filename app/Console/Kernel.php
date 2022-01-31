<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:check_tokens_every5')->everyFiveMinutes()
            ->onSuccess(function () use (&$log) {
            // The task succeeded...
                Log::info('SUCCESS: command:check_tokens_every5 ');
            })
            ->onFailure(function () use (&$log) {
                // The task failed...
                Log::error('FAILED: command:check_tokens_every5 ');
            });

        $schedule->command('command:load_streams_every15')->everyFifteenMinutes()
            ->onSuccess(function () use (&$log) {
            // The task succeeded...
                Log::info('SUCCESS: command:load_streams_every15 ');
            })
            ->onFailure(function () use (&$log) {
                // The task failed...
                Log::error('FAILED: command:load_streams_every15 ');
            });
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
