<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JatahCuti extends Model
{
    protected $table = 'jatah_cuti';

    // Pastikan semua kolom masuk fillable — ini penyebab update tidak berubah
    protected $fillable = [
        'karyawan_id',
        'tahun',
        'jatah_awal',
        'jatah',
    ];

    protected $casts = [
        'tahun'      => 'integer',
        'jatah_awal' => 'integer',
        'jatah'      => 'integer',
    ];

    /* ── Relationships ──────────────────────────────────────── */

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /* ── Helper: sisa cuti (jika ada tabel pengajuan_cuti) ─── */

    public function getSisaCutiAttribute(): int
    {
        return max(0, $this->jatah);
    }
}
