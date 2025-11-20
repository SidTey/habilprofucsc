<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ForceRestoreDatabase;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ForceRestoreDatabase::class,
    ];

    /**
     * Define the application's command schedule (R1.16).
     * 
     * R1.16: Este proceso se activará automáticamente cada minuto
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ejecuta el comando sync:ucsc cada minuto sin solapamiento (R1.16)
        $schedule->command('sync:ucsc')
                 ->everyMinute()
                 ->name('sync-ucsc-worker')
                 ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
