<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\MonthlyInvoice::class,
        Commands\CleanupStorage::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('voice:monthly')
            ->monthly();

        // Safe disk reclaim ONLY: import xlsx/csv, public/tmp merge PDFs, debugbar.
        // Never deletes MyIB tracking label PDFs (uploads/PNX_LABEL).
        $schedule->command('storage:cleanup --days=14 --tmp-hours=24 --debugbar-hours=24')
            ->dailyAt('03:15')
            ->withoutOverlapping();
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
