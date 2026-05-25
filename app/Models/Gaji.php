<?php

namespace App\Models;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    protected $table = 'gaji';

    protected $fillable = [
        'karyawan_id',
        'bulan',
        'tahun',
        'total_hadir',
        'gaji_harian',
        'total_gaji',
        'tanggal_hitung'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }


}
