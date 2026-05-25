<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShiftApproval extends Model
{
    use HasFactory;

    protected $table = 'shift_approvals';

    protected $fillable = [
        'ajukan_shift_id',
        'step',
        'approved_by',
        'status',
        'catatan',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function ajukanShift()
    {
        return $this->belongsTo(AjukanShift::class, 'ajukan_shift_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getRoleLabelAttribute(): string
    {
        $labels = [
            'admin'       => 'Admin Departemen',
            'gm'          => 'General Manager',
            'super_admin' => 'Super Admin',
        ];
        return $labels[$this->step] ?? strtoupper($this->step);
    }

    public function getApproverUser()
    {
        $departemenId = $this->ajukanShift->departemen_id;

        switch ($this->step) {
            case 'admin':
                return User::where('role', 'admin')
                    ->whereHas('karyawan', fn($q) => $q->where('departemen_id', $departemenId))
                    ->first();
            case 'gm':
                return User::where('role', 'gm')->first();
            case 'super_admin':
                return User::where('role', 'super_admin')->first();
            default:
                return null;
        }
    }
}