<?php

namespace Intranet\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Intranet\Console\Commands\SaoAnnexes;
use Intranet\Console\Commands\SaoConnect;
use Intranet\Console\Commands\SendDailyEmails;
use Intranet\Console\Commands\CreateDailyGuards;
use Intranet\Console\Commands\NotifyDailyFaults;
use Intranet\Console\Commands\SendFctEmails;
use Intranet\Console\Commands\UploadAnexes;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SendDailyEmails::class,
        CreateDailyGuards::class,
        NotifyDailyFaults::class,
        SendFctEmails::class,
        UploadAnexes::class,
        SaoConnect::class,
        SaoAnnexes::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('fault:Daily')
                ->dailyAt('23:00');
        $schedule->command('email:Daily')
                ->dailyAt('23:30');
        $schedule->command('guard:Daily')
                ->dailyAt('07:45');
        $schedule->command('fct:Daily')
                ->dailyAt('07:30');
        $schedule->command('sao:connect')
            ->dailyAt('23:00');
        $schedule->command('sao:annexes')
            ->dailyAt('07:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require_once base_path('routes/console.php');
    }
}
