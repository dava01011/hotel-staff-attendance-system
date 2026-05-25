<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanLiburPengganti extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_libur_pengganti';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'alasan',
        'file_pendukung',
        'status',
        'current_step',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function approvals()
    {
        return $this->hasMany(LiburPenggantiApproval::class, 'pengajuan_id')->orderByRaw("
    CASE step
        WHEN 'admin' THEN 1
        WHEN 'hrd' THEN 2
        WHEN 'gm' THEN 3
        WHEN 'super_admin' THEN 4
        ELSE 99
    END
");
    }

    public function currentApproval()
    {
        return $this->hasOne(LiburPenggantiApproval::class, 'pengajuan_id')
            ->where('step', $this->current_step)
            ->where('status', 'pending');
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->status === 'disetujui') return 100;
        if ($this->status === 'ditolak') return 0;

        $total = $this->approvals()->count();
        if ($total === 0) return 0;

        $completed = $this->approvals()->where('status', 'disetujui')->count();
        return (int) round(($completed / $total) * 100);
    }

    public function isFullyApproved(): bool
    {
        return $this->approvals()->where('status', '!=', 'disetujui')->count() === 0;
    }

    public static function getApprovalSteps(string $role): array
    {
        return match($role) {
            'karyawan'    => ['admin', 'super_admin'],
            'admin'       => ['gm', 'super_admin'],
            'gm'          => ['super_admin'],
            'super_admin' => [],
            default       => ['admin', 'super_admin'],
        };
    }

    public function createApprovalSteps(string $pemohonRole): void
    {
        $steps = self::getApprovalSteps($pemohonRole);

        foreach ($steps as $index => $step) {
            $this->approvals()->create([
                'step'   => $step,
                'status' => 'pending',
            ]);

            if ($index === 0) {
                $this->update(['current_step' => $step]);
            }
        }

        if (empty($steps)) {
            $this->update([
                'status'       => 'disetujui',
                'current_step' => null,
            ]);
        }
    }
}