<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CutiApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'cuti_id',
        'step',
        'approved_by',
        'status',
        'catatan',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    /**
     * Relasi ke Cuti
     */
    public function cuti()
    {
        return $this->belongsTo(Cuti::class);
    }

    /**
     * Relasi ke User yang approve
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Label role untuk tampilan
     */
    public function getRoleLabelAttribute()
    {
        $labels = [
            'admin'       => 'Admin Departemen',
            'gm'          => 'General Manager',
            'super_admin' => 'Super Admin',
        ];

        return $labels[$this->step] ?? strtoupper($this->step);
    }

    /**
     * Alias untuk role_label
     */
    public function getStepNameAttribute()
    {
        return $this->role_label;
    }

    /**
     * Dapatkan user yang berwenang menyetujui step ini
     * berdasarkan departemen karyawan yang mengajukan cuti
     */
    public function getApproverUser()
    {
        $karyawan = $this->cuti->karyawan;
        $deptId   = $karyawan->departemen_id;

        switch ($this->step) {
            case 'admin':
                // Cari user dengan role admin di departemen yang sama
                return User::where('role', 'admin')
                    ->whereHas('karyawan', function ($q) use ($deptId) {
                        $q->where('departemen_id', $deptId);
                    })
                    ->first();


            case 'gm':
                // Cari user dengan role gm
                return User::where('role', 'gm')->first();

            case 'super_admin':
                // Super Admin mana saja
                return User::where('role', 'super_admin')->first();

            default:
                return null;
        }
    }
}