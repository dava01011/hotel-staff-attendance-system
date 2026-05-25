<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\HariLiburNasional;
use App\Models\Karyawan;
use App\Models\KaryawanShiftPattern;
use App\Models\Cuti;
use App\Models\LiburPengganti;
use Carbon\Carbon;

class AbsensiDetectionService
{
    /**
     * LOGIC PRE-GENERATE ABSENSI (NEW - PROACTIVE):
     *
     * Setiap hari jam 00:00 (12 malam):
     * 1. Generate SEMUA record absensi untuk hari ini
     * 2. Default status:
     *    - ALPA (untuk hari kerja sesuai shift pattern)
     *    - LIBUR (untuk hari libur shift/nasional/cuti)
     * 3. Karyawan absen → UPDATE status HADIR
     * 4. Support shift lintas hari (jam 12 malam)
     * 5. Auto-add saldo libur pengganti (jika absen di hari libur nasional)
     */

    /**
     * Pre-generate semua absensi untuk tanggal tertentu
     *
     * Biasanya dipanggil jam 00:00 (12 malam)
     * Untuk generate absensi hari ini
     *
     * @param Carbon $date Tanggal untuk generate absensi
     * @return array
     */
    public static function preGenerateAbsensiForDate(Carbon $date): array
    {
        $karyawan = Karyawan::with('user')->get();
        $created = [];
        $skipped = [];

        foreach ($karyawan as $k) {
            try {
                $result = self::createAbsensiRecord($k, $date);

                if ($result['status'] === 'created') {
                    $created[] = $result['data'];
                } else {
                    $skipped[] = $result['data'];
                }
            } catch (\Exception $e) {
                $skipped[] = [
                    'karyawan_id' => $k->id,
                    'nama' => $k->user->nama,
                    'tanggal' => $date->toDateString(),
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'date' => $date->toDateString(),
            'created' => $created,
            'skipped' => $skipped,
            'total_created' => count($created),
            'total_skipped' => count($skipped),
        ];
    }

    /**
     * Create atau skip absensi record untuk 1 karyawan
     *
     * Return: [
     *   'status' => 'created' | 'skipped',
     *   'data' => [...]
     * ]
     */
    private static function createAbsensiRecord(Karyawan $karyawan, Carbon $date): array
    {
        // STEP 1: Check apakah sudah ada record
        $existing = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $date)
            ->first();

        if ($existing) {
            return [
                'status' => 'skipped',
                'data' => [
                    'karyawan_id' => $karyawan->id,
                    'nama' => $karyawan->user->nama,
                    'tanggal' => $date->toDateString(),
                    'reason' => 'already_exists',
                ],
            ];
        }

        // STEP 2: Determine status default
        $statusData = self::determineDefaultStatus($karyawan, $date);

        // STEP 3: Create record
        $absensi = Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => $date->toDateString(),
            'jam_masuk' => null,
            'jam_pulang' => null,
            'latitude' => null,
            'longitude' => null,
            'foto_masuk' => null,
            'foto_pulang' => null,
            'face_valid' => 0,
            'face_confidence' => null,
            'face_distance' => null,
            'verification_method' => 'system',
            'status' => $statusData['status'],
            'jenis_libur_pengganti' => $statusData['jenis_libur_pengganti'],
        ]);

        return [
            'status' => 'created',
            'data' => [
                'karyawan_id' => $karyawan->id,
                'nama' => $karyawan->user->nama,
                'tanggal' => $date->toDateString(),
                'status' => $statusData['status'],
                'reason' => $statusData['reason'],
            ],
        ];
    }

    /**
     * Determine default status untuk tanggal tertentu
     *
     * Return: [
     *   'status' => 'alpa' | 'libur' | 'cuti',
     *   'jenis_libur_pengganti' => null | true | false,
     *   'reason' => '...'
     * ]
     */
    private static function determineDefaultStatus(Karyawan $karyawan, Carbon $date): array
    {
        $namaHari = self::getNamaHari($date);

        // PRIORITY 1: Check cuti disetujui
        if (self::hasApprovedCuti($karyawan, $date)) {
            return [
                'status' => 'cuti',
                'jenis_libur_pengganti' => null,
                'reason' => 'cuti_disetujui',
            ];
        }

        // PRIORITY 2: Check hari libur nasional
        if (self::isHariLiburNasional($date)) {
            return [
                'status' => 'libur',
                'jenis_libur_pengganti' => null,
                'reason' => 'libur_nasional',
            ];
        }

        // PRIORITY 3: Check shift pattern (libur/kerja)
        $pattern = KaryawanShiftPattern::getPatternForDate($karyawan->id, $date);

        if ($pattern && isset($pattern[$namaHari])) {
            if ($pattern[$namaHari]->tipe === 'libur') {
                return [
                    'status' => 'libur',
                    'jenis_libur_pengganti' => null,
                    'reason' => 'libur_shift',
                ];
            }
        }

        // DEFAULT: Hari kerja (default ALPA, tunggu absensi)
        return [
            'status' => 'alpa',
            'jenis_libur_pengganti' => null,
            'reason' => 'hari_kerja',
        ];
    }

    /**
     * Update absensi ketika karyawan masuk
     *
     * Called saat karyawan absen (check in)
     * 1. Detect shift (dari jadwal shift pattern)
     * 2. Jika shift lintas_hari && jam_absen < jam_masuk → record untuk BESOK
     * 3. Update status ALPA → HADIR
     * 4. Jika hari libur nasional → add saldo libur pengganti
     *
     * @param Karyawan $karyawan
     * @param array $absensiData ['jam_masuk', 'latitude', 'longitude', 'foto_masuk', 'face_valid', 'face_confidence', 'face_distance', 'verification_method']
     * @param Carbon $dateAbsen Tanggal absensi (bisa tanggal sebelumnya jika shift lintas hari)
     * @return Absensi
     */
    public static function recordPresence(Karyawan $karyawan, array $absensiData, Carbon $dateAbsen = null): Absensi
    {
        if (!$dateAbsen) {
            $dateAbsen = Carbon::today();
        }

        // STEP 1: Get shift dari jadwal hari ini
        $shift = self::getShiftForDate($karyawan, $dateAbsen);

        // STEP 2: Handle shift lintas hari (jam 12 malam)
        if ($shift && $shift->lintas_hari) {
            // Jam absensi dari data
            $jamAbsen = Carbon::createFromFormat('H:i:s', $absensiData['jam_masuk'] ?? '00:00:00');
            $jamMasukShift = Carbon::createFromFormat('H:i:s', $shift->jam_masuk);

            // Jika jam absen < jam masuk shift → ini shift malam kemarin
            // Record untuk besok hari
            if ($jamAbsen < $jamMasukShift) {
                $dateAbsen = $dateAbsen->addDay(); // Untuk hari berikutnya
            }
        }

        // STEP 3: Find atau create absensi record
        $absensi = Absensi::firstOrCreate(
            [
                'karyawan_id' => $karyawan->id,
                'tanggal' => $dateAbsen->toDateString(),
            ],
            [
                'jam_masuk' => null,
                'jam_pulang' => null,
                'latitude' => null,
                'longitude' => null,
                'foto_masuk' => null,
                'foto_pulang' => null,
                'face_valid' => 0,
                'face_confidence' => null,
                'face_distance' => null,
                'verification_method' => 'system',
                'status' => 'alpa', // Default
                'jenis_libur_pengganti' => null,
            ]
        );

        // STEP 4: Update dengan data absensi
        $absensi->update([
            'jam_masuk' => $absensiData['jam_masuk'] ?? null,
            'latitude' => $absensiData['latitude'] ?? null,
            'longitude' => $absensiData['longitude'] ?? null,
            'foto_masuk' => $absensiData['foto_masuk'] ?? null,
            'face_valid' => $absensiData['face_valid'] ?? 0,
            'face_confidence' => $absensiData['face_confidence'] ?? null,
            'face_distance' => $absensiData['face_distance'] ?? null,
            'verification_method' => $absensiData['verification_method'] ?? 'manual',
            'status' => 'hadir', // Update status ke hadir
        ]);

        // STEP 5: Handle hari libur nasional (add saldo libur pengganti)
        if (self::isHariLiburNasional($dateAbsen)) {
            LiburPengganti::addSaldo($karyawan->id, 1);
        }

        return $absensi;
    }

    /**
     * Get shift untuk karyawan pada tanggal tertentu
     */
    public static function getShiftForDate(Karyawan $karyawan, Carbon $date): ?object
    {
        $namaHari = self::getNamaHari($date);
        $pattern = KaryawanShiftPattern::getPatternForDate($karyawan->id, $date);

        if ($pattern && isset($pattern[$namaHari]) && $pattern[$namaHari]->tipe === 'kerja' && $pattern[$namaHari]->shift_id) {
            $shiftId = $pattern[$namaHari]->shift_id;
            return \App\Models\Shift::find($shiftId);
        }

        return null;
    }

    /**
     * Get nama hari dari Carbon date
     */
    public static function getNamaHari(Carbon $date): string
    {
        return KaryawanShiftPattern::getNamaHari($date);
    }

    /**
     * Check apakah ada cuti yang disetujui untuk karyawan pada tanggal tersebut
     */
    public static function hasApprovedCuti(Karyawan $karyawan, Carbon $date): bool
    {
        return Cuti::where('karyawan_id', $karyawan->id)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->exists();
    }

    /**
     * Check apakah tanggal tertentu adalah hari libur nasional
     */
    public static function isHariLiburNasional(Carbon $date): bool
    {
        return HariLiburNasional::isHariLiburNasional($date);
    }

    /**
     * Pre-generate absensi untuk DATE RANGE
     */
    public static function preGenerateAbsensiForDateRange(Carbon $startDate, Carbon $endDate): array
    {
        $allResults = [
            'total_created' => 0,
            'total_skipped' => 0,
            'created_details' => [],
            'skipped_details' => [],
            'date_range' => "{$startDate->toDateString()} - {$endDate->toDateString()}",
        ];

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $result = self::preGenerateAbsensiForDate($currentDate);

            $allResults['total_created'] += $result['total_created'];
            $allResults['total_skipped'] += $result['total_skipped'];
            $allResults['created_details'] = array_merge(
                $allResults['created_details'],
                $result['created']
            );
            $allResults['skipped_details'] = array_merge(
                $allResults['skipped_details'],
                $result['skipped']
            );

            $currentDate->addDay();
        }

        return $allResults;
    }

    /**
     * Get status symbol untuk laporan
     */
    public static function getStatusSymbol(string $status): string
    {
        $map = [
            'hadir' => '✓',
            'terlambat' => 'T',
            'sakit' => 'S',
            'cuti' => 'C',
            'alpa' => 'A',
            'libur' => 'L',
        ];

        return $map[$status] ?? '-';
    }

    /**
     * Get summary untuk dashboard
     */
    public static function getSummaryForDate(Carbon $date): array
    {
        $absensi = Absensi::whereDate('tanggal', $date)->get();

        return [
            'tanggal' => $date->toDateString(),
            'total_karyawan' => Karyawan::count(),
            'hadir' => $absensi->where('status', 'hadir')->count(),
            'terlambat' => $absensi->where('status', 'terlambat')->count(),
            'sakit' => $absensi->where('status', 'sakit')->count(),
            'cuti' => $absensi->where('status', 'cuti')->count(),
            'alpa' => $absensi->where('status', 'alpa')->count(),
            'libur' => $absensi->where('status', 'libur')->count(),
        ];
    }
}
