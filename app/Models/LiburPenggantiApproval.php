<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LiburPenggantiApproval extends Model
{
    use HasFactory;

    protected $table = 'libur_pengganti_approvals';

    protected $fillable = [
        'pengajuan_id',
        'step',
        'approved_by',
        'status',
        'catatan',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * ⭐ TAMBAHKAN INI - Accessor untuk role label (SAMA seperti CutiApproval)
     */
    protected $appends = ['role_label'];

    /**
     * ⭐ TAMBAHKAN INI - Get role label
     */
    public function getRoleLabelAttribute(): string
    {
        $labels = [
            'admin'       => 'Admin Departemen',
            'gm'          => 'General Manager',
            'super_admin' => 'Super Admin',
        ];
        return $labels[$this->step] ?? ucfirst($this->step);
    }

    /**
     * Relasi ke pengajuan
     */
    public function pengajuan()
    {
        return $this->belongsTo(PengajuanLiburPengganti::class, 'pengajuan_id');
    }

    /**
     * Relasi ke approver user
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get approver user berdasarkan step
     */
    public function getApproverUser()
    {
        try {
            $karyawan = $this->pengajuan->karyawan;
            if (!$karyawan) {
                return null;
            }

            $deptId = $karyawan->departemen_id;

            switch ($this->step) {
                case 'admin':
                    return User::where('role', 'admin')
                        ->whereHas('karyawan', function ($q) use ($deptId) {
                            $q->where('departemen_id', $deptId);
                        })
                        ->first();

                case 'gm':
                    return User::where('role', 'gm')->first();
                case 'super_admin':
                    return User::where('role', 'super_admin')->first();
                default:
                    return null;
            }
        } catch (\Exception $e) {
            \Log::error('Error getting approver user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get approval status label
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending'   => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }
}