<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cuti';

    protected $fillable = [
        'karyawan_id',
        'jenis_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'file_pendukung',
        'status',
        'current_step',
        'is_bentrok',
        'catatan_admin'
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'is_bentrok'      => 'boolean'
    ];

    /**
     * Relasi ke Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Relasi ke Jenis Cuti
     */
    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_id');
    }

    /**
     * Relasi ke Approval Steps
     */
    public function approvals()
    {
        return $this->hasMany(CutiApproval::class)->orderBy('id');
    }

    /**
     * Ambil approval step yang sedang pending sesuai current_step
     */
    public function currentApproval()
    {
        return $this->hasOne(CutiApproval::class)
            ->where('step', $this->current_step)
            ->where('status', 'pending');
    }

    /**
     * Jumlah hari cuti
     */
    public function getJumlahHariAttribute()
    {
        if (!$this->tanggal_mulai || !$this->tanggal_selesai) {
            return 0;
        }
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    /**
     * Cek apakah user tertentu bisa approve cuti ini
     */
    public function canBeApprovedBy($user): bool
    {
        // Hanya bisa approve jika status masih pending
        if ($this->status !== 'pending') {
            return false;
        }

        // Pemohon tidak boleh approve cuti sendiri
        if ($this->karyawan->user_id === $user->id) {
            return false;
        }

        // Ambil current approval
        $current = $this->currentApproval;
        if (!$current) {
            return false;
        }

        // Dapatkan user yang seharusnya approve step ini
        $expectedApprover = $current->getApproverUser();

        // Jika tidak ada yang diharapkan (misal belum di-set), fallback ke pengecekan role
        if (!$expectedApprover) {
            // Fallback: cocokkan role user dengan step
            $roleStepMap = [
                'admin'   => 'admin',
                'hrd'     => 'hrd',
                'gm'      => 'gm',
                'super_admin' => 'super_admin',
            ];
            return ($roleStepMap[$user->role] ?? null) === $this->current_step;
        }

        // Bandingkan ID user
        return $expectedApprover->id === $user->id;
    }

    /**
     * Tentukan workflow approval berdasarkan role pengaju
     */
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

    /**
     * Progress persentase approval
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->status === 'disetujui') {
            return 100;
        }
        if ($this->status === 'ditolak') {
            return 0;
        }

        $total = $this->approvals()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $this->approvals()->where('status', 'disetujui')->count();
        return (int) round(($completed / $total) * 100);
    }

    /**
     * Warna badge status
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'warning',
            'disetujui' => 'success',
            'ditolak'   => 'danger',
            default     => 'secondary',
        };
    }

    /**
     * Cek apakah semua approval sudah disetujui
     */
    public function isFullyApproved(): bool
    {
        return $this->approvals()->where('status', '!=', 'disetujui')->count() === 0;
    }

    /**
     * Buat approval steps berdasarkan role pengaju
     */
    public function createApprovalSteps(string $pemohonRole): void
    {
        $steps = self::getApprovalSteps($pemohonRole);

        foreach ($steps as $index => $step) {
            $this->approvals()->create([
                'step'   => $step,
                'status' => 'pending',
            ]);

            // Set current_step ke step pertama
            if ($index === 0) {
                $this->update(['current_step' => $step]);
            }
        }

        // Jika tidak ada step (HRD/Admin), langsung setujui
        if (empty($steps)) {
            $this->update([
                'status'       => 'disetujui',
                'current_step' => null,
            ]);
        }
    }
}