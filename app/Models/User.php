<?php

namespace App\Models;

use App\Models\Karyawan;
use App\Models\Notifikasi;
use App\Models\ActivityLog;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // protected $casts = [
    //     'is_active' => 'boolean',
    // ];

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'user_id');
    }

        public function activityLog()
    {
        return $this->hasOne(ActivityLog::class, 'user_id');
    }


    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }


    public function isKaryawan()
    {
        return $this->role === 'karyawan';
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }

}
