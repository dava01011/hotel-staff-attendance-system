<?php

namespace App\Console\Commands;

use App\Models\Karyawan;
use App\Models\JatahCuti;
use Illuminate\Console\Command;

class ResetCutiTahunan extends Command
{
    protected $signature   = 'cuti:reset';
    protected $description = 'Reset jatah cuti awal tahun (jalankan tiap 1 Januari)';

    public function handle()
    {
        // Guard: hanya boleh jalan di bulan Januari
        if (now()->month !== 1) {
            $this->warn('Bukan bulan Januari. Command dibatalkan.');
            return;
        }

        $tahun    = now()->year;
        $karyawan = Karyawan::where('status', 'aktif')->get();

        $count = 0;

        foreach ($karyawan as $k) {
            JatahCuti::updateOrCreate(
                [
                    'karyawan_id' => $k->id,
                    'tahun'       => $tahun,
                ],
                [
                    // BUG FIX: jatah_awal juga harus di-set
                    'jatah_awal' => 0,
                    'jatah'      => 0,
                ]
            );
            $count++;
        }

        activity_log(
            'jatah_cuti',
            'reset',
            "Reset jatah cuti tahunan {$tahun} untuk {$count} karyawan"
        );

        $this->info("Jatah cuti {$tahun} berhasil di-reset ke 0 untuk {$count} karyawan.");
    }
}
