<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * Define the application's command schedule.
   */
  protected function schedule(Schedule $schedule): void
  {
    // Application forwarding runs via the queue (ForwardApplicationToBackend),
    // not the scheduler. Nothing is scheduled at the moment.
  }

  /**
   * Register the commands for the application.
   */
  protected function commands(): void
  {
    $this->load(__DIR__.'/Commands');
    require base_path('routes/console.php');
  }
}
