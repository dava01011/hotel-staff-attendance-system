<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'pembuat_id',
        'judul',
        'konten',
        'tipe',
        'departemen_id',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }
}
