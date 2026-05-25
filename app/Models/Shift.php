<?php

namespace App\Models;

use App\Models\Karyawan;
use App\Models\AjukanShift;
use App\Models\KaryawanShiftPattern;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shift';

    protected $fillable = [
        'kode',
        // 'jenis',
        'jam_masuk',
        'jam_pulang',
        'toleransi_menit',
        'lintas_hari'
    ];


    // Relasi ke KaryawanShiftPattern
    public function karyawanShiftPatterns()
    {
        return $this->hasMany(KaryawanShiftPattern::class);
    }

    public function pengajuanShiftAwal()
    {
        return $this->hasMany(AjukanShift::class, 'shift_awal_id');
    }

    public function pengajuanShiftPengganti()
    {
        return $this->hasMany(AjukanShift::class, 'shift_pengganti_id');
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }

}
