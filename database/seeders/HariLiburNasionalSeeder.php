<?php

namespace Database\Seeders;

use App\Models\HariLiburNasional;
use App\Models\Karyawan;
use App\Models\LiburPengganti;
use Illuminate\Database\Seeder;

class HariLiburNasionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Simplified: 1 tabel saja
     * - Fixed: setup once, is_recurring = true → auto-repeat
     * - Dynamic: input per tahun
     */
    public function run(): void
    {
        // ===== FIXED HOLIDAYS (TANGGAL TETAP) =====
        // Setup once, is_recurring = true → otomatis repeat setiap tahun

        $fixedHolidays = [
            [1, 1, 'Tahun Baru', 'Hari Libur Nasional'],
            [5, 1, 'Hari Buruh Internasional', 'Hari Libur Nasional'],
            [6, 1, 'Pancasila', 'Hari Libur Nasional'],
            [8, 17, 'Kemerdekaan Indonesia', 'Hari Libur Nasional'],
            [12, 25, 'Hari Natal', 'Hari Libur Nasional'],
        ];

        foreach ($fixedHolidays as [$bulan, $hari, $nama, $keterangan]) {
            HariLiburNasional::createFixed($bulan, $hari, $nama, $keterangan);
        }

        // ===== DYNAMIC/LUNAR HOLIDAYS (TANGGAL BERVARIASI) =====
        // Input per tahun (2026 sample)

        // Idul Fitri 2026 (3 hari)
        HariLiburNasional::createDynamic('2026-04-10', 'Hari Raya Idul Fitri', 'dynamic', 'Hari Raya Idul Fitri (H)');
        HariLiburNasional::createDynamic('2026-04-11', 'Hari Raya Idul Fitri', 'dynamic', 'Hari Raya Idul Fitri (H+1)');
        HariLiburNasional::createDynamic('2026-04-12', 'Cuti Bersama Lebaran', 'dynamic', 'Cuti Bersama Lebaran');

        // Idul Adha 2026 (2 hari)
        HariLiburNasional::createDynamic('2026-05-27', 'Hari Raya Idul Adha', 'dynamic', 'Hari Raya Idul Adha (H)');
        HariLiburNasional::createDynamic('2026-05-28', 'Cuti Bersama Idul Adha', 'dynamic', 'Cuti Bersama Idul Adha');

        // Tahun Baru Imlek 2026 (1 hari)
        HariLiburNasional::createDynamic('2026-02-17', 'Tahun Baru Imlek', 'dynamic', 'Tahun Baru Imlek');

        // ===== INITIALIZE LIBUR PENGGANTI =====

        $karyawan = Karyawan::all();
        foreach ($karyawan as $k) {
            LiburPengganti::getOrCreate($k->id);
        }

        $this->command->info('✅ Hari libur nasional (simplified) seeded successfully!');
        $this->command->info('');
        $this->command->info('📋 FIXED HOLIDAYS (Auto-recurring):');
        $this->command->info('   - Tahun Baru (1 Januari)');
        $this->command->info('   - Hari Buruh (1 Mei)');
        $this->command->info('   - Pancasila (1 Juni)');
        $this->command->info('   - Kemerdekaan (17 Agustus)');
        $this->command->info('   - Natal (25 Desember)');
        $this->command->info('');
        $this->command->info('📅 DYNAMIC HOLIDAYS (2026):');
        $this->command->info('   - Idul Fitri (3 hari: 10-12 April)');
        $this->command->info('   - Idul Adha (2 hari: 27-28 Mei)');
        $this->command->info('   - Tahun Baru Imlek (1 hari: 17 Februari)');
        $this->command->info('');
        $this->command->info('💡 Tip: Untuk tahun 2027+, cukup input dynamic holidays saja.');
        $this->command->info('   Fixed holidays otomatis recurring setiap tahun.');
        $this->command->info("👥 Initialized libur pengganti untuk {$karyawan->count()} karyawan");
    }
}
