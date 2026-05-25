<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Auth\RegisterController;

class RegisterController extends Controller
{
    /**
     * Tampilkan form register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi
     */
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nama.required' => 'Nama lengkap harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            // Buat user baru dengan status pending
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'karyawan', // Otomatis set sebagai karyawan
                'status' => 'pending', // Menunggu approval admin
            ]);

            // Redirect ke login dengan pesan sukses
            return redirect()->route('login')->with('success',
                'Pendaftaran berhasil! Akun Anda akan aktif setelah disetujui oleh admin. ' .
                'Silakan cek email untuk informasi lebih lanjut.'
            );

        } catch (\Exception $e) {
            \Log::error('Register Error: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }
}
