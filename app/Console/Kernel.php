<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('cuti:generate')->monthlyOn(1, '00:01');
        $schedule->command('cuti:reset')->yearlyOn(1, 1, '00:01');
        $schedule->command('cuti:update-bulanan')->monthlyOn(1, '00:05')->withoutOverlapping()->runInBackground();
        $schedule->command('absensi:cleanup-photos --days=90 --force')
            ->daily()
            ->at('02:00')
            ->timezone('Asia/Jakarta');
// crontab -e

# Add this line:
// * * * * * cd /var/www/html/your-project && php artisan schedule:run >> /dev/null 2>&1


        // EXISTING SCHEDULE
        // $schedule->command('inspire')->hourly();

        // ===== ABSENT DETECTION =====
        // Jalankan setiap hari pukul 23:59 (malam sebelum hari baru)
        // Detect absent untuk hari kemarin
         $schedule->command('absensi:pre-generate')
            ->everyMinute()
            // ->dailyAt('00:00')
            ->withoutOverlapping()
            ->onSuccess(function () {
                // Log success (optional)
                \Illuminate\Support\Facades\Log::info('✅ Absensi pre-generated for new day');
            })
            ->onFailure(function () {
                // Log failure (optional)
                \Illuminate\Support\Facades\Log::error('❌ Absensi pre-generation failed');
            });
        // ===== CLEANUP (OPTIONAL) =====
        // Cleanup old logs setiap bulan
        $schedule->command('log:clear')
            ->monthlyOn(1, '03:00');

        // Optional: Jika mau detect untuk hari ini (bukan kemarin), bisa pakai ini:
        // $schedule->command('absensi:detect-absent', ['date' => now()->toDateString()])
        //     ->dailyAt('23:59');
        
        //buat test schedule (running tiap menit)
        // $schedule->command('inspire')->everyMinute();
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
