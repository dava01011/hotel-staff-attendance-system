<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // ── Rate Limiting ────────────────────────────────────────
        $throttleKey = Str::transliterate(
            Str::lower($request->input('email')) . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->withInput($request->except('password'));
        }

        // ── Cek user & validasi ──────────────────────────────────
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'email' => 'Email tidak terdaftar.',
            ])->withInput($request->except('password'));
        }

        if ($user->status !== 'aktif') {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ])->withInput($request->except('password'));
        }

        if (!Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'password' => 'Password salah.',
            ])->withInput($request->except('password'));
        }

        // ── Login berhasil ───────────────────────────────────────
        RateLimiter::clear($throttleKey);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        activity_log(
            'auth',
            'login',
            'Login ke sistem dengan email: ' . $user->email,
            $user->id,
            $user->role
        );

        // Cek dual access untuk semua role admin
        $hasAdminRole    = in_array($user->role, ['admin', 'super_admin']);
        $hasKaryawanData = $user->karyawan && $user->karyawan->status === 'aktif';

        if ($hasAdminRole && $hasKaryawanData) {
            return redirect()->route('role.select');
        }

        return $this->redirectBasedOnRole();
    }

    /**
     * Tampilkan halaman pemilihan role
     */
    public function showRoleSelection()
    {
        $user = Auth::user();

        $hasAdminRole    = in_array($user->role, ['admin', 'super_admin']);
        $hasKaryawanData = $user->karyawan && $user->karyawan->status === 'aktif';

        if (!$hasAdminRole || !$hasKaryawanData) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.role-select', compact('user'));
    }

    /**
     * Set role yang dipilih dan redirect
     */
    public function setRole(Request $request)
    {
        $request->validate([
            'selected_role' => 'required|in:admin,karyawan',
        ]);

        $user = Auth::user();

        $hasAdminRole    = in_array($user->role, ['admin', 'super_admin']);
        $hasKaryawanData = $user->karyawan && $user->karyawan->status === 'aktif';

        if (!$hasAdminRole || !$hasKaryawanData) {
            return redirect()->route('login');
        }

        Session::put('active_role', $request->selected_role);

        activity_log(
            'auth',
            'select_mode',
            'Memilih mode: ' . ucfirst($request->selected_role)
        );

        if ($request->selected_role === 'admin') {
            Session::put('original_role', $user->role);
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('karyawan.dashboard');
    }

    /**
     * Switch role (untuk user yang sudah login)
     */
    public function switchRole(Request $request)
    {
        $request->validate([
            'switch_to' => 'required|in:admin,karyawan',
        ]);

        $user = Auth::user();

        $hasAdminRole    = in_array($user->role, ['admin', 'super_admin']);
        $hasKaryawanData = $user->karyawan && $user->karyawan->status === 'aktif';

        if (!$hasAdminRole || !$hasKaryawanData) {
            return back()->with('error', 'Anda tidak memiliki akses ganda.');
        }

        Session::put('active_role', $request->switch_to);

        activity_log(
            'auth',
            'switch_mode',
            'Beralih ke mode ' . ucfirst($request->switch_to)
        );

        if ($request->switch_to === 'admin') {
            Session::put('original_role', $user->role);
            return redirect()->route('admin.dashboard')->with('success', 'Berhasil beralih ke mode Admin');
        }

        return redirect()->route('karyawan.dashboard')->with('success', 'Berhasil beralih ke mode Karyawan');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $activeMode = Session::get('active_role', 'default');
            activity_log(
                'auth',
                'logout',
                'Logout dari sistem (mode: ' . $activeMode . ')'
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
        
    }

    private function redirectBasedOnRole()
    {
        $user = Auth::user();

        if (Session::has('active_role')) {
            $activeRole = Session::get('active_role');
            return $activeRole === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('karyawan.dashboard');
        }

        if (in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('karyawan.dashboard');
    }
}
