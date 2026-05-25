<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WajahRequest extends Model
{
    protected $table = 'wajah_requests';

    protected $fillable = [
        'karyawan_id',
        'user_id',
        'alasan',
        'status',
        'reviewed_by',
        'catatan_admin',
        'reviewed_at',
        'captured_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'captured_at' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
