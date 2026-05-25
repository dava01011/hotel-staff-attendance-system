<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'target_role',
        'judul',
        'pesan',
        'type',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function scopeForMode($query)
{
    return $query->where('target_role', active_mode());
}

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
