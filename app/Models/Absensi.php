<?php
// Model Absensi
namespace App\Models;

use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'latitude',
        'longitude',
        'foto_masuk',
        'foto_pulang',
        'face_valid',
        'face_confidence',
        'face_distance',
        'verification_method',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'face_valid' => 'boolean',
        'face_confidence' => 'decimal:2',
        'face_distance' => 'decimal:6',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
