<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Collection\Collection;

class KaryawanShiftPattern extends Model
{
    protected $table = 'karyawan_shift_pattern';

    protected $fillable = [
        'karyawan_id',
        'hari',
        'tipe',
        'shift_id',
        'is_default',
        'minggu_ke',
        'tahun',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship ke Karyawan
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /**
     * Relationship ke Shift
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    /**
     * Scope: Get pattern DEFAULT untuk karyawan
     */
    public function scopeDefault($query, $karyawanId)
    {
        return $query
            ->where('karyawan_id', $karyawanId)
            ->where('is_default', true)
            ->where('is_active', true);
    }

    /**
     * Scope: Get pattern WEEKLY untuk minggu & tahun tertentu
     */
    public function scopeWeekly($query, $karyawanId, $mingguKe, $tahun)
    {
        return $query
            ->where('karyawan_id', $karyawanId)
            ->where('minggu_ke', $mingguKe)
            ->where('tahun', $tahun)
            ->where('is_active', true);
    }

    /**
     * ===== MAIN METHOD =====
     * Get pattern untuk karyawan pada tanggal spesifik
     *
     * Logic:
     * 1. Cek pattern WEEKLY (override)
     * 2. Jika tidak ada → pakai pattern DEFAULT
     *
     * @param int $karyawanId
     * @param Carbon $date
     * @return Collection (keyBy('hari'))
     */
    public static function getPatternForDate($karyawanId, Carbon $date)
    {
        $mingguKe = $date->weekOfYear;
        $tahun = $date->year;
        $namaHari = self::getNamaHari($date);

        // STEP 1: Cek pattern WEEKLY (override)
        $weeklyPattern = self::weekly($karyawanId, $mingguKe, $tahun)->get();

        if ($weeklyPattern->count() > 0) {
            return $weeklyPattern->keyBy('hari');
        }

        // STEP 2: Pakai pattern DEFAULT
        return self::default($karyawanId)->get()->keyBy('hari');
    }

    /**
     * Get tipe (kerja/libur) untuk karyawan pada hari tertentu
     *
     * @param int $karyawanId
     * @param string $hari ('senin', 'selasa', etc)
     * @param Carbon $date
     * @return string ('kerja', 'libur', null)
     */
    public static function getTipeForDay($karyawanId, $hari, Carbon $date)
    {
        $pattern = self::getPatternForDate($karyawanId, $date);
        return $pattern[$hari]->tipe ?? null;
    }

    /**
     * Check apakah karyawan libur pada hari tersebut
     */
    public static function isLibur($karyawanId, $hari, Carbon $date)
    {
        return self::getTipeForDay($karyawanId, $hari, $date) === 'libur';
    }

    /**
     * Check apakah karyawan seharusnya kerja pada hari tersebut
     */
    public static function shouldWork($karyawanId, $hari, Carbon $date)
    {
        return self::getTipeForDay($karyawanId, $hari, $date) === 'kerja';
    }

    /**
     * Get label hari dalam bahasa Indonesia
     */
    public static function getLabelHari($hari)
    {
        $labels = [
            'minggu' => 'Minggu',
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
            'sabtu' => 'Sabtu',
        ];

        return $labels[$hari] ?? $hari;
    }

    /**
     * Get nama hari dari Carbon date
     * Kembalikan: 'senin', 'selasa', etc
     */
    public static function getNamaHari(Carbon $date)
    {
        $dayOfWeek = $date->dayOfWeek; // 0=Minggu, 1=Senin, ..., 6=Sabtu

        $hari = [
            0 => 'minggu',
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
        ];

        return $hari[$dayOfWeek] ?? null;
    }

    /**
     * Get minggu ke dari Carbon date
     */
    public static function getWeekNumber(Carbon $date)
    {
        return $date->weekOfYear;
    }

    /**
     * Get tahun dari Carbon date
     */
    public static function getYear(Carbon $date)
    {
        return $date->year;
    }

    /**
     * Check apakah karyawan punya pattern weekly untuk minggu/tahun tertentu
     */
    public static function hasWeeklyOverride($karyawanId, $mingguKe, $tahun)
    {
        return self::weekly($karyawanId, $mingguKe, $tahun)->exists();
    }

    /**
     * Get tanggal mulai & selesai untuk minggu ke berapa
     */
    public static function getWeekDateRange($mingguKe, $tahun)
    {
        $date = Carbon::now()->setISODate($tahun, $mingguKe, 1); // Senin minggu ke-X
        $startDate = $date->copy()->startOfWeek();
        $endDate = $date->copy()->endOfWeek();

        return [
            'start' => $startDate,
            'end' => $endDate,
        ];
    }

    /**
     * Get pattern mingguan yang aktif (untuk display di karyawan dashboard)
     */
    public static function getActiveWeeklyPattern($karyawanId, Carbon $date = null)
    {
        $date = $date ?? now();
        $mingguKe = $date->weekOfYear;
        $tahun = $date->year;

        return self::weekly($karyawanId, $mingguKe, $tahun)->get();
    }

    /**
     * Get pattern default untuk karyawan
     */
    public static function getDefaultPattern($karyawanId)
    {
        return self::default($karyawanId)->get()->keyBy('hari');
    }

    /**
     * Create or update weekly override
     *
     * Digunakan saat admin/karyawan mau set pattern mingguan
     */
    public static function setWeeklyOverride($karyawanId, $mingguKe, $tahun, array $patterns)
    {
        // Hapus pattern lama untuk minggu ini
        self::where('karyawan_id', $karyawanId)
            ->where('minggu_ke', $mingguKe)
            ->where('tahun', $tahun)
            ->delete();

        // Create pattern baru
        foreach ($patterns as $hari => $data) {
            self::create([
                'karyawan_id' => $karyawanId,
                'hari' => $hari,
                'tipe' => is_array($data) ? $data['tipe'] : $data,
                'shift_id' => is_array($data) ? ($data['shift_id'] ?? null) : null,
                'is_default' => false,
                'minggu_ke' => $mingguKe,
                'tahun' => $tahun,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Create or update default pattern
     *
     * Digunakan saat admin set pattern default (permanent)
     */
    public static function setDefaultPattern($karyawanId, array $patterns)
    {
        // Hapus pattern default lama
        self::where('karyawan_id', $karyawanId)
            ->where('is_default', true)
            ->delete();

        // Create pattern default baru
        foreach ($patterns as $hari => $data) {
            self::create([
                'karyawan_id' => $karyawanId,
                'hari' => $hari,
                'tipe' => is_array($data) ? $data['tipe'] : $data,
                'shift_id' => is_array($data) ? ($data['shift_id'] ?? null) : null,
                'is_default' => true,
                'minggu_ke' => null,
                'tahun' => null,
                'is_active' => true,
            ]);
        }
    }
}
