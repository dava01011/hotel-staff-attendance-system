<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiburPengganti extends Model
{
    protected $table = 'libur_pengganti';

    protected $fillable = [
        'karyawan_id',
        'saldo',
        'terakhir_diupdate',
    ];

    protected $casts = [
        'terakhir_diupdate' => 'datetime',
    ];

    /**
     * Relationship ke Karyawan
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /**
     * Get atau create saldo libur pengganti untuk karyawan
     */
    public static function getOrCreate($karyawanId): LiburPengganti
    {
        return self::firstOrCreate(
            ['karyawan_id' => $karyawanId],
            ['saldo' => 0]
        );
    }

    /**
     * Get saldo libur pengganti untuk karyawan
     */
    public static function getSaldo($karyawanId): int
    {
        return self::where('karyawan_id', $karyawanId)->value('saldo') ?? 0;
    }

    /**
     * Tambah saldo (ketika karyawan kerja di hari libur nasional)
     */
    public static function addSaldo($karyawanId, int $jumlah = 1): void
    {
        $libur = self::getOrCreate($karyawanId);
        $libur->saldo += $jumlah;
        $libur->terakhir_diupdate = now();
        $libur->save();
    }

    /**
     * Kurangi saldo (ketika karyawan menggunakan libur pengganti)
     */
    public static function subtractSaldo($karyawanId, int $jumlah = 1): bool
    {
        $libur = self::getOrCreate($karyawanId);

        // Check apakah saldo cukup
        if ($libur->saldo < $jumlah) {
            return false;
        }

        $libur->saldo -= $jumlah;
        $libur->terakhir_diupdate = now();
        $libur->save();

        return true;
    }

    /**
     * Check apakah karyawan punya saldo cukup
     */
    public static function hasSaldo($karyawanId, int $jumlah = 1): bool
    {
        return self::getSaldo($karyawanId) >= $jumlah;
    }

    /**
     * Reset saldo (untuk keperluan admin)
     */
    public static function resetSaldo($karyawanId): void
    {
        $libur = self::getOrCreate($karyawanId);
        $libur->saldo = 0;
        $libur->terakhir_diupdate = now();
        $libur->save();
    }

    /**
     * Get summary semua karyawan dan saldo mereka
     */
    public static function getAllWithSaldo()
    {
        return self::with('karyawan.user')
            ->get()
            ->map(function ($libur) {
                return [
                    'karyawan_id' => $libur->karyawan_id,
                    'nama' => $libur->karyawan->user->nama ?? 'N/A',
                    'saldo' => $libur->saldo,
                    'terakhir_diupdate' => $libur->terakhir_diupdate,
                ];
            });
    }
}
