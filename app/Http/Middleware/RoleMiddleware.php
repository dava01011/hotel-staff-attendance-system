<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $allowedRoles = explode('|', $roles);

        // Cek apakah user memiliki dual access (admin + karyawan)
        $hasAdminRole = in_array($user->role, ['admin', 'super_admin']);
        $hasKaryawanData = $user->karyawan && $user->karyawan->status === 'aktif';
        $hasDualAccess = $hasAdminRole && $hasKaryawanData;

        if ($hasDualAccess) {
            // User punya akses ganda, cek active_role di session
            $activeRole = Session::get('active_role');

            // Jika belum ada active_role, redirect ke halaman pilihan
            if (!$activeRole) {
                return redirect()->route('role.select');
            }

            // Validasi apakah active_role sesuai dengan route yang diakses
            if ($activeRole === 'admin') {
                // Sedang di mode admin
                $adminRoles = ['admin', 'super_admin'];

                // Cek apakah route mengizinkan role admin
                $hasAdminAccess = false;
                foreach ($adminRoles as $adminRole) {
                    if (in_array($adminRole, $allowedRoles)) {
                        $hasAdminAccess = true;
                        break;
                    }
                }

                if (!$hasAdminAccess) {
                    // Trying to access karyawan route while in admin mode
                    return redirect()->route('admin.dashboard')
                        ->with('error', 'Anda sedang dalam mode Admin. Beralih ke mode Karyawan untuk mengakses halaman ini.');
                }

                // Cek apakah role user sesuai dengan allowed roles
                if (!in_array($user->role, $allowedRoles)) {
                    abort(403, 'Akses ditolak. Anda tidak memiliki hak akses ke halaman ini.');
                }

            } else if ($activeRole === 'karyawan') {
                // Sedang di mode karyawan
                if (!in_array('karyawan', $allowedRoles)) {
                    // Trying to access admin route while in karyawan mode
                    return redirect()->route('karyawan.dashboard')
                        ->with('error', 'Anda sedang dalam mode Karyawan. Beralih ke mode Admin untuk mengakses halaman ini.');
                }

                // Validasi data karyawan
                if (!$user->karyawan || $user->karyawan->status !== 'aktif') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->withErrors(['error' => 'Akun karyawan tidak aktif.']);
                }
            }
        } else {
            // User tidak punya dual access, validasi role normal
            $userRole = $user->role;

            // Cek apakah role user ada di allowed roles
            if (!in_array($userRole, $allowedRoles)) {
                // Special case: role yang punya data karyawan bisa akses route karyawan
                if (in_array('karyawan', $allowedRoles) && $hasKaryawanData) {
                    $userRole = 'karyawan';
                } else {
                    abort(403, 'Akses ditolak. Anda tidak memiliki hak akses ke halaman ini.');
                }
            }

            // Validasi khusus untuk routes karyawan
            if (in_array('karyawan', $allowedRoles)) {

                if (!$user->karyawan || $user->karyawan->status !== 'aktif') {
                    // Kecuali super_admin & admin murni
                    if (!in_array($user->role, ['super_admin', 'admin'])) {
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        return redirect()->route('login')
                            ->withErrors(['error' => 'Akun karyawan tidak aktif.']);
                    }
                }
            }
        }

        return $next($request);
    }
}
