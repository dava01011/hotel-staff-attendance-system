<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

if (!function_exists('active_mode')) {
    function active_mode()
    {
        // Prioritas: session > user role
        if (Session::has('active_role')) {
            return Session::get('active_role');
        }

        // Fallback ke role user
        if (Auth::check()) {
            $user = Auth::user();

            // Jika admin tapi punya data karyawan, default ke admin
            if (in_array($user->role, ['admin', 'super_admin'])) {
                return 'admin';
            }

            // Jika karyawan
            if ($user->karyawan && $user->karyawan->status === 'aktif') {
                return 'karyawan';
            }

            return $user->role;
        }

        return null;
    }
}

if (!function_exists('is_admin_mode')) {
    function is_admin_mode()
    {
        $mode = active_mode();
        return $mode === 'admin' || $mode === 'super_admin';
    }
}

if (!function_exists('is_karyawan_mode')) {
    function is_karyawan_mode()
    {
        return active_mode() === 'karyawan';
    }
}

function activity_log($module, $action, $description = null)
{
    ActivityLog::create([
        'user_id' => Auth::id(),
        'role' => active_mode(), // admin / karyawan /super
        'module' => $module,
        'action' => $action,
        'description' => $description,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);
}
