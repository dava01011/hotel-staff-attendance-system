<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class HariLiburNasional extends Model
{
    protected $table = 'hari_libur_nasional';

    protected $fillable = [
        'tanggal',
        'nama',
        'tipe',
        'bulan_tetap',
        'hari_tetap',
        'tahun',
        'is_recurring',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Scope: Get hari libur nasional yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check apakah tanggal tertentu adalah hari libur nasional
     *
     * @param Carbon $date
     * @return boolean
     */
    public static function isHariLiburNasional(Carbon $date): bool
    {
        return self::aktif()
            ->whereDate('tanggal', $date->toDateString())
            ->exists();
    }

    /**
     * Get detail hari libur nasional untuk tanggal tertentu
     *
     * @param Carbon $date
     * @return HariLiburNasional|null
     */
    public static function getHariLiburNasional(Carbon $date): ?HariLiburNasional
    {
        return self::aktif()
            ->whereDate('tanggal', $date->toDateString())
            ->first();
    }

    /**
     * Get semua hari libur nasional untuk bulan tertentu
     */
    public static function getForMonth(int $bulan, int $tahun)
    {
        return self::aktif()
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get();
    }

    /**
     * Get semua hari libur nasional untuk tahun tertentu
     */
    public static function getForYear(int $tahun)
    {
        return self::aktif()
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get();
    }

    /**
     * Create fixed holiday (yang akan recur setiap tahun)
     *
     * Contoh:
     * HariLiburNasional::createFixed(1, 1, 'Tahun Baru', 'Hari Libur Nasional')
     * → akan auto-recur setiap 1 Januari
     */
    public static function createFixed(int $bulan, int $hari, string $nama, string $keterangan = null): HariLiburNasional
    {
        $tahunSekarang = date('Y');
        $tanggal = Carbon::createFromDate($tahunSekarang, $bulan, $hari);

        return self::create([
            'tanggal' => $tanggal,
            'nama' => $nama,
            'tipe' => 'fixed',
            'bulan_tetap' => $bulan,
            'hari_tetap' => $hari,
            'tahun' => $tahunSekarang,
            'is_recurring' => true, // Auto-recur setiap tahun
            'keterangan' => $keterangan,
            'is_active' => true,
        ]);
    }

    /**
     * Create dynamic/manual holiday untuk tahun tertentu
     *
     * Contoh:
     * HariLiburNasional::createDynamic('2026-04-10', 'Idul Fitri', 'dynamic', 'Hari Raya Idul Fitri')
     */
    public static function createDynamic(string $tanggal, string $nama, string $tipe = 'dynamic', string $keterangan = null): HariLiburNasional
    {
        $date = Carbon::createFromFormat('Y-m-d', $tanggal);

        return self::create([
            'tanggal' => $date,
            'nama' => $nama,
            'tipe' => $tipe, // dynamic atau manual
            'bulan_tetap' => null,
            'hari_tetap' => null,
            'tahun' => $date->year,
            'is_recurring' => false,
            'keterangan' => $keterangan,
            'is_active' => true,
        ]);
    }

    /**
     * Auto-generate fixed holidays untuk tahun depan
     *
     * Jalankan setiap akhir tahun untuk generate fixed holidays tahun depan
     */
    public static function autoGenerateFixedForYear(int $tahun): array
    {
        $created = [];

        // Get semua fixed holidays yang is_recurring = true
        $fixedHolidays = self::where('tipe', 'fixed')
            ->where('is_recurring', true)
            ->where('is_active', true)
            ->groupBy('bulan_tetap', 'hari_tetap') // Ambil unique bulan & hari saja
            ->get();

        foreach ($fixedHolidays as $holiday) {
            // Check apakah sudah ada untuk tahun ini
            $exists = self::where('tanggal', Carbon::createFromDate($tahun, $holiday->bulan_tetap, $holiday->hari_tetap))
                ->exists();

            if (!$exists) {
                $tanggal = Carbon::createFromDate($tahun, $holiday->bulan_tetap, $holiday->hari_tetap);

                self::create([
                    'tanggal' => $tanggal,
                    'nama' => $holiday->nama,
                    'tipe' => 'fixed',
                    'bulan_tetap' => $holiday->bulan_tetap,
                    'hari_tetap' => $holiday->hari_tetap,
                    'tahun' => $tahun,
                    'is_recurring' => true,
                    'keterangan' => $holiday->keterangan,
                    'is_active' => true,
                ]);

                $created[] = [
                    'tanggal' => $tanggal->toDateString(),
                    'nama' => $holiday->nama,
                ];
            }
        }

        return $created;
    }

    /**
     * Get label tipe
     */
    public function getTipeLabel(): string
    {
        return match($this->tipe) {
            'fixed' => 'Tanggal Tetap (Recur)',
            'dynamic' => 'Tanggal Bervariasi',
            'manual' => 'Input Manual',
            default => $this->tipe,
        };
    }

    /**
     * Get summary untuk admin
     */
    public static function getSummary()
    {
        return [
            'total' => self::aktif()->count(),
            'tahun_ini' => self::aktif()->whereYear('tanggal', date('Y'))->count(),
            'fixed' => self::aktif()->where('tipe', 'fixed')->count(),
            'dynamic' => self::aktif()->where('tipe', 'dynamic')->count(),
            'manual' => self::aktif()->where('tipe', 'manual')->count(),
            'recurring' => self::aktif()->where('is_recurring', true)->count(),
        ];
    }
}
