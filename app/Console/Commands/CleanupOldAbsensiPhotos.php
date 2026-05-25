<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Absensi;
use Carbon\Carbon;

class CleanupOldAbsensiPhotos extends Command
{
    /**
     * Command signature
     */
    protected $signature = 'absensi:cleanup-photos
                            {--days=90 : Hapus foto lebih lama dari X hari}
                            {--dry-run : Preview tanpa menghapus}
                            {--force : Skip confirmation}';

    /**
     * Command description
     */
    protected $description = 'Hapus foto absensi yang sudah lama untuk menghemat storage';

    /**
     * Execute command
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("╔══════════════════════════════════════════╗");
        $this->info("║   CLEANUP FOTO ABSENSI LAMA             ║");
        $this->info("╚══════════════════════════════════════════╝");
        $this->newLine();

        $this->info("📅 Cutoff Date: {$cutoffDate->format('Y-m-d H:i:s')}");
        $this->info("🗑️  Will delete photos older than {$days} days");

        if ($dryRun) {
            $this->warn("🔍 DRY RUN MODE - No files will be deleted");
        }

        $this->newLine();

        // Query absensi lama
        $query = Absensi::where('tanggal', '<', $cutoffDate)
            ->where(function($q) {
                $q->whereNotNull('foto_masuk')
                  ->orWhereNotNull('foto_pulang');
            });

        $totalRecords = $query->count();

        if ($totalRecords === 0) {
            $this->info("✅ No old photos found. Nothing to clean up.");
            return Command::SUCCESS;
        }

        $this->info("📊 Found {$totalRecords} absensi records with photos to process");
        $this->newLine();

        // Confirmation
        if (!$force && !$dryRun) {
            if (!$this->confirm('Continue with deletion?')) {
                $this->warn('Cleanup cancelled.');
                return Command::SUCCESS;
            }
        }

        // Progress bar
        $bar = $this->output->createProgressBar($totalRecords);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $bar->setMessage('Starting...');
        $bar->start();

        $deletedCount = 0;
        $failedCount = 0;
        $totalSize = 0;
        $errors = [];

        // Process each record
        $oldAbsensi = $query->get();

        foreach ($oldAbsensi as $absensi) {
            $bar->setMessage("Processing ID: {$absensi->id}");

            // Process foto masuk
            if ($absensi->foto_masuk) {
                $result = $this->deletePhoto($absensi->foto_masuk, $dryRun);

                if ($result['success']) {
                    $deletedCount++;
                    $totalSize += $result['size'];

                    if (!$dryRun) {
                        $absensi->foto_masuk = null;
                    }
                } else {
                    $failedCount++;
                    $errors[] = "Failed to delete: {$absensi->foto_masuk}";
                }
            }

            // Process foto pulang
            if ($absensi->foto_pulang) {
                $result = $this->deletePhoto($absensi->foto_pulang, $dryRun);

                if ($result['success']) {
                    $deletedCount++;
                    $totalSize += $result['size'];

                    if (!$dryRun) {
                        $absensi->foto_pulang = null;
                    }
                } else {
                    $failedCount++;
                    $errors[] = "Failed to delete: {$absensi->foto_pulang}";
                }
            }

            // Save changes
            if (!$dryRun && ($absensi->isDirty('foto_masuk') || $absensi->isDirty('foto_pulang'))) {
                $absensi->save();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Results
        $totalSizeMB = round($totalSize / (1024 * 1024), 2);
        $totalSizeGB = round($totalSizeMB / 1024, 2);

        $this->info("╔══════════════════════════════════════════╗");
        $this->info("║   CLEANUP RESULTS                        ║");
        $this->info("╚══════════════════════════════════════════╝");
        $this->newLine();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Records Processed', number_format($totalRecords)],
                ['Photos Deleted', number_format($deletedCount)],
                ['Failed', number_format($failedCount)],
                ['Space Freed (MB)', number_format($totalSizeMB, 2)],
                ['Space Freed (GB)', number_format($totalSizeGB, 3)],
                ['Mode', $dryRun ? 'DRY RUN (no changes)' : 'LIVE'],
            ]
        );

        // Show errors if any
        if (!empty($errors) && count($errors) <= 10) {
            $this->newLine();
            $this->warn("⚠️  Errors:");
            foreach ($errors as $error) {
                $this->line("   - {$error}");
            }
        } elseif (count($errors) > 10) {
            $this->newLine();
            $this->warn("⚠️  {$failedCount} errors occurred. Check logs for details.");
        }

        // Activity log
        if (!$dryRun && function_exists('activity_log')) {
            activity_log(
                'cleanup',
                'photos',
                "Cleanup {$deletedCount} foto absensi lama (>{$days} days), freed {$totalSizeMB} MB"
            );
        }

        $this->newLine();

        if ($dryRun) {
            $this->info("🔍 DRY RUN completed. Run without --dry-run to actually delete files.");
        } else {
            $this->info("✅ Cleanup completed successfully!");
        }

        return Command::SUCCESS;
    }

    /**
     * Delete a photo file
     *
     * @param string $filepath
     * @param bool $dryRun
     * @return array
     */
    private function deletePhoto($filepath, $dryRun = false)
    {
        try {
            if (!Storage::disk('public')->exists($filepath)) {
                return [
                    'success' => true,
                    'size' => 0,
                    'message' => 'File not found (already deleted)'
                ];
            }

            $size = Storage::disk('public')->size($filepath);

            if (!$dryRun) {
                $deleted = Storage::disk('public')->delete($filepath);

                if (!$deleted) {
                    return [
                        'success' => false,
                        'size' => 0,
                        'message' => 'Delete operation failed'
                    ];
                }
            }

            return [
                'success' => true,
                'size' => $size,
                'message' => 'Deleted successfully'
            ];

        } catch (\Exception $e) {
            \Log::error('Error deleting photo', [
                'filepath' => $filepath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'size' => 0,
                'message' => $e->getMessage()
            ];
        }
    }
}
