<?php

namespace Database\Seeders;

use App\Models\KaryawanShiftPattern;
use Illuminate\Database\Seeder;

class KaryawanShiftPatternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =====================================================
        // LEVEL 1: DEFAULT PATTERN (PERMANENT)
        // =====================================================

        // KARYAWAN ID 2 (Adi/Admin)
        // Default: Libur Jumat-Sabtu, Kerja Minggu-Kamis
        $this->createDefaultPattern(2, [
            'minggu' => 'kerja',
            'senin' => 'kerja',
            'selasa' => 'kerja',
            'rabu' => 'kerja',
            'kamis' => 'kerja',
            'jumat' => 'libur',
            'sabtu' => 'libur',
        ]);

        // KARYAWAN ID 3 (Budi/Manager)
        // Default: Libur Minggu-Senin, Kerja Selasa-Sabtu
        $this->createDefaultPattern(3, [
            'minggu' => 'libur',
            'senin' => 'libur',
            'selasa' => 'kerja',
            'rabu' => 'kerja',
            'kamis' => 'kerja',
            'jumat' => 'kerja',
            'sabtu' => 'kerja',
        ]);

        // KARYAWAN ID 1 (karyawan)
        // Default: Libur Sabtu-Minggu (standard), Kerja Senin-Jumat
        $this->createDefaultPattern(1, [
            'minggu' => 'libur',
            'senin' => 'kerja',
            'selasa' => 'kerja',
            'rabu' => 'kerja',
            'kamis' => 'kerja',
            'jumat' => 'kerja',
            'sabtu' => 'libur',
        ]);

        // =====================================================
        // LEVEL 2: WEEKLY OVERRIDE PATTERN (MINGGUAN)
        // =====================================================

        // SAMPLE: Minggu 11 tahun 2026 (2026-03-15 s/d 2026-03-21)
        // Adi & Budi tukar hari libur mereka

        // Adi (ID 2): Override minggu 11 - Libur Minggu-Senin (tukar dengan Budi)
        $this->createWeeklyOverride(2, 11, 2026, [
            'minggu' => 'libur',    // Normally kerja → libur
            'senin' => 'libur',     // Normally kerja → libur
            'selasa' => 'kerja',
            'rabu' => 'kerja',
            'kamis' => 'kerja',
            'jumat' => 'kerja',     // Normally libur → kerja
            'sabtu' => 'kerja',     // Normally libur → kerja
        ]);

        // Budi (ID 3): Override minggu 11 - Libur Jumat-Sabtu (tukar dengan Adi)
        $this->createWeeklyOverride(3, 11, 2026, [
            'minggu' => 'kerja',    // Normally libur → kerja
            'senin' => 'kerja',     // Normally libur → kerja
            'selasa' => 'kerja',
            'rabu' => 'kerja',
            'kamis' => 'kerja',
            'jumat' => 'libur',     // Normally kerja → libur
            'sabtu' => 'libur',     // Normally kerja → libur
        ]);

        $this->command->info('✅ Karyawan shift patterns seeded successfully!');
        $this->command->info('');
        $this->command->info('📋 DEFAULT PATTERNS:');
        $this->command->info('   Karyawan 2: Libur Jumat-Sabtu');
        $this->command->info('   Karyawan 3: Libur Minggu-Senin');
        $this->command->info('   Karyawan 1: Libur Sabtu-Minggu');
        $this->command->info('');
        $this->command->info('📅 WEEKLY OVERRIDES (Minggu 11/2026 - 15-21 Mar):');
        $this->command->info('   Karyawan 2: Tukar dengan Karyawan 3 (Libur Minggu-Senin)');
        $this->command->info('   Karyawan 3: Tukar dengan Karyawan 2 (Libur Jumat-Sabtu)');
    }

    /**
     * Helper: Create default pattern
     */
    private function createDefaultPattern($karyawanId, array $patterns)
    {
        foreach ($patterns as $hari => $tipe) {
            KaryawanShiftPattern::create([
                'karyawan_id' => $karyawanId,
                'hari' => $hari,
                'tipe' => $tipe,
                'is_default' => true,
                'minggu_ke' => null,
                'tahun' => null,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Helper: Create weekly override pattern
     */
    private function createWeeklyOverride($karyawanId, $mingguKe, $tahun, array $patterns)
    {
        foreach ($patterns as $hari => $tipe) {
            KaryawanShiftPattern::create([
                'karyawan_id' => $karyawanId,
                'hari' => $hari,
                'tipe' => $tipe,
                'is_default' => false,
                'minggu_ke' => $mingguKe,
                'tahun' => $tahun,
                'is_active' => true,
            ]);
        }
    }
}
