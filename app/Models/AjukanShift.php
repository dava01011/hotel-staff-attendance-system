<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AjukanShift extends Model
{
    use HasFactory;

    protected $table = 'ajukan_shifts';

    protected $fillable = [
        'departemen_id',
        'shift_lama_id',
        'shift_baru_id',
        'tanggal_mulai',
        'tanggal_selesai',
        // 'jenis',
        'requested_by',
        'approved_by',
        'alasan',
        'status',
        'current_step',
        'catatan_admin'
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Relasi
    public function pemohon()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function shiftLama()
    {
        return $this->belongsTo(Shift::class, 'shift_lama_id');
    }

    public function shiftBaru()
    {
        return $this->belongsTo(Shift::class, 'shift_baru_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    // Relasi ke approvals
    public function approvals()
    {
        return $this->hasMany(ShiftApproval::class, 'ajukan_shift_id')->orderBy('id');
    }

    public function currentApproval()
    {
        return $this->hasOne(ShiftApproval::class, 'ajukan_shift_id')
            ->where('step', $this->current_step)
            ->where('status', 'pending');
    }

    // Progress percentage
    public function getProgressPercentageAttribute(): int
    {
        if ($this->status === 'disetujui') return 100;
        if ($this->status === 'ditolak') return 0;

        $total = $this->approvals()->count();
        if ($total === 0) return 0;

        $completed = $this->approvals()->where('status', 'disetujui')->count();
        return (int) round(($completed / $total) * 100);
    }

    // Cek apakah semua approval selesai
    public function isFullyApproved(): bool
    {
        return $this->approvals()->where('status', '!=', 'disetujui')->count() === 0;
    }

    // Tentukan langkah approval berdasarkan role pemohon
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
   

    // Buat approval steps
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