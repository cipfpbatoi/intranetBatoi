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
                ->dailyAt('21:00');
        $schedule->command('email:Daily')
                ->dailyAt('21:15');
        $schedule->command('guards:Daily')
                ->dailyAt('07:00');
        $schedule->command('fct:Daily')
                ->dailyAt('08:10');
        $schedule->command('sao:connect')->weekly()->fridays()->at('8:00');
        $schedule->command('sao:connect')->weekly()->tuesdays()->at('8:00');
        $schedule->command('sao:annexes')
            ->weeklyOn(1, '8:30');
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
