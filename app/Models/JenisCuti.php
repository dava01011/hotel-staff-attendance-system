<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    protected $table = 'jenis_cuti';

    protected $fillable = [
        'nama',
        'deskripsi',
        'butuh_file',
        'potong_jatah',
        'aktif',
    ];

    protected $casts = [
        'butuh_file'   => 'boolean',
        'potong_jatah' => 'boolean',
        'aktif'        => 'boolean',
    ];

    // Scope hanya yang aktif
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
