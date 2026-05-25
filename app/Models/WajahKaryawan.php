<?php

namespace App\Models;

use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Model;

class WajahKaryawan extends Model
{
    protected $table = 'wajah_karyawan';

    protected $fillable = [
        'karyawan_id',
        'face_encoding',
        'face_image',
        'confidence_score',
        'registered_at',
        'registered_by'
    ];

    protected $casts = [
        'face_encoding' => 'array',
        'confidence_score' => 'decimal:2',
        'registered_at' => 'datetime'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}
