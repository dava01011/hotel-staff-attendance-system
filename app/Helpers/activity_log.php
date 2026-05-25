<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('activity_log')) {
    /**
     * Helper function untuk mencatat activity log
     *
     * @param string $module - Module yang diakses (cuti, absensi, shift, dll)
     * @param string $action - Action yang dilakukan (create, update, delete, approve, reject)
     * @param string $description - Deskripsi detail aktivitas
     * @param int|null $userId - User ID (optional, default auth user)
     * @param string|null $role - Role (optional, default active_mode)
     * @return void
     */
    function activity_log(
        string $module,
        string $action,
        string $description,
        ?int $userId = null,
        ?string $role = null
    ) {
        try {
            ActivityLog::create([
                'user_id'     => $userId ?? Auth::id(),
                'role'        => $role ?? (Auth::check() ? active_mode() : 'system'),
                'module'      => $module,
                'action'      => $action,
                'description' => $description,
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silent fail - jangan sampai error log mengganggu proses utama
            \Log::error('Failed to create activity log: ' . $e->getMessage());
        }
    }
}

if (!function_exists('active_mode')) {
    /**
     * Get active mode (admin/karyawan) for current user
     *
     * @return string
     */
    function active_mode(): string
    {
        if (!Auth::check()) {
            return 'guest';
        }

        // Cek apakah ada active_role di session (untuk dual role user)
        if (session()->has('active_role')) {
            return session('active_role');
        }

        // Default ke role user
        $role = Auth::user()->role;

        // Jika super_admin atau admin, return 'admin'
        if (in_array($role, ['super_admin', 'admin'])) {
            return 'admin';
        }

        // Jika karyawan, return 'karyawan'
        return 'karyawan';
    }
}
