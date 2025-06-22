<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
      protected $commands = [
    \App\Console\Commands\DeleteExpiredOtps::class,
];

    protected function schedule(Schedule $schedule): void
    {
        // Schedule tasks here
        // Example: Run a task every minute
        $schedule->command('otps:delete-expired-otps')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
        
    }
    protected $routeMiddleware = [
        // other middleware ...
'tenant.auth' => \App\Http\Middleware\TenantAuth::class,
    ];
    
  
}