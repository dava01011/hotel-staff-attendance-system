<?php

namespace App\Console\Commands;

use App\Models\Karyawan;
use App\Models\JatahCuti;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateMonthlyCuti extends Command
{
    protected $signature   = 'cuti:update-bulanan';
    protected $description = 'Menambahkan 1 jatah cuti per bulan (Feb–Des)';

    public function handle()
    {
        $tahun = now()->year;
        $count = 0;

        DB::transaction(function () use ($tahun, &$count) {
            $karyawanAktif = Karyawan::with('jabatan')->where('status', 'aktif')->get();

            foreach ($karyawanAktif as $karyawan) {
                $increment = $karyawan->jabatan ? $karyawan->jabatan->jatah_cuti_bulanan : 0;
                
                if ($increment <= 0) continue;

                $jatah = JatahCuti::firstOrCreate(
                    [
                        'karyawan_id' => $karyawan->id,
                        'tahun'       => $tahun,
                    ],
                    [
                        'jatah_awal' => 0,
                        'jatah'      => 0,
                    ]
                );

                $jatah->increment('jatah_awal', $increment);
                $jatah->increment('jatah', $increment);
                $count++;
            }
        });

        if ($count > 0) {
            activity_log(
                'jatah_cuti',
                'generate',
                'Generate jatah cuti bulanan bulan ' . now()->isoFormat('MMMM YYYY') . " ({$count} karyawan)"
            );
        }

        $this->info("Jatah cuti bulan " . now()->isoFormat('MMMM YYYY') . " berhasil ditambah untuk {$count} karyawan.");
    }
}
