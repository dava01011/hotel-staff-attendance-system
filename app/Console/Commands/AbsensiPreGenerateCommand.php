<?php

namespace App\Console\Commands;

use App\Services\AbsensiDetectionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AbsensiPreGenerateCommand extends Command
{
    /**
     * Command name
     */
    protected $signature = 'absensi:pre-generate {date? : Tanggal untuk pre-generate (Y-m-d, default: hari ini)}';

    /**
     * Command description
     */
    protected $description = 'Pre-generate absensi untuk semua karyawan pada tanggal tertentu (default status: ALPA untuk hari kerja, LIBUR untuk hari libur)';

    /**
     * Execute the command
     */
    public function handle(): int
    {
        // Parse tanggal (default: hari ini)
        $dateStr = $this->argument('date');
        $date = $dateStr
            ? Carbon::createFromFormat('Y-m-d', $dateStr)
            : Carbon::today();

        $this->info("🔍 Pre-generating absensi untuk tanggal: " . $date->format('Y-m-d (l)'));
        $this->line("");

        // Run pre-generation
        $result = AbsensiDetectionService::preGenerateAbsensiForDate($date);

        // Output hasil
        $this->info("✅ HASIL PRE-GENERATE:");
        $this->info("   Total Created: " . $result['total_created']);
        $this->info("   Total Skipped: " . $result['total_skipped']);

        if (!empty($result['created'])) {
            $this->line("");
            $this->info("📝 Created Records ({$result['total_created']}):");
            foreach ($result['created'] as $record) {
                $statusLabel = match($record['reason']) {
                    'hari_kerja' => '(ALPA - Hari Kerja)',
                    'libur_shift' => '(LIBUR - Shift)',
                    'libur_nasional' => '(LIBUR - Nasional)',
                    'cuti_disetujui' => '(CUTI - Disetujui)',
                    default => '',
                };
                $this->line("   ✓ {$record['nama']} (ID: {$record['karyawan_id']}) - Status: {$record['status']} {$statusLabel}");
            }
        }

        if (!empty($result['skipped'])) {
            $this->line("");
            $this->info("⏭️  Skipped Records ({$result['total_skipped']}):");
            foreach ($result['skipped'] as $record) {
                $reason = $record['reason'] ?? $record['error'] ?? 'unknown';
                $this->line("   - {$record['nama']} (ID: {$record['karyawan_id']}) - Reason: {$reason}");
            }
        }

        $this->line("");
        $this->info("✨ Pre-generation complete!");
        $this->line("");

        // Show summary
        $summary = AbsensiDetectionService::getSummaryForDate($date);
        $this->info("📊 SUMMARY:");
        $this->info("   Hadir: {$summary['hadir']}");
        $this->info("   Terlambat: {$summary['terlambat']}");
        $this->info("   Sakit: {$summary['sakit']}");
        $this->info("   Cuti: {$summary['cuti']}");
        $this->info("   ALPA: {$summary['alpa']}");
        $this->info("   LIBUR: {$summary['libur']}");

        return Command::SUCCESS;
    }
}
