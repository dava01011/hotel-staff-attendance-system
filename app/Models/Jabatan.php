<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';

    protected $fillable = [
        'nama_jabatan',
        'jatah_cuti_bulanan',
        'tipe_gaji',
        'gaji_pokok',
        'gaji_harian',
    ];

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }

    public function getGajiAttribute()
    {
        return $this->tipe_gaji === 'harian'
            ? $this->gaji_harian
            : $this->gaji_pokok;
    }
}
