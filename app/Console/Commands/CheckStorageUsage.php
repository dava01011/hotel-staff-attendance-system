<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckStorageUsage extends Command
{
    protected $signature = 'storage:check';
    protected $description = 'Cek penggunaan storage foto absensi';

    public function handle()
    {
        $this->info('📊 STORAGE USAGE REPORT');
        $this->info('======================');

        $path = storage_path('app/public/absensi');

        if (!is_dir($path)) {
            $this->error("Directory tidak ditemukan: {$path}");
            return Command::FAILURE;
        }

        $totalSize = 0;
        $fileCount = 0;
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path)
        );

        foreach ($files as $file) {
            if ($file->isFile()) {
                $totalSize += $file->getSize();
                $fileCount++;
            }
        }

        $totalSizeMB = round($totalSize / (1024 * 1024), 2);
        $totalSizeGB = round($totalSizeMB / 1024, 2);
        $avgSizeKB = $fileCount > 0 ? round(($totalSize / $fileCount) / 1024, 2) : 0;

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Files', number_format($fileCount)],
                ['Total Size (MB)', number_format($totalSizeMB, 2)],
                ['Total Size (GB)', number_format($totalSizeGB, 2)],
                ['Average File Size (KB)', number_format($avgSizeKB, 2)],
                ['Storage Path', $path],
            ]
        );

        if ($totalSizeGB > 5) {
            $this->warn('⚠️  Storage sudah lebih dari 5 GB! Consider cleanup.');
        }

        if ($totalSizeGB > 10) {
            $this->error('🚨 Storage sudah lebih dari 10 GB! Segera cleanup!');
        }

        return Command::SUCCESS;
    }
}
