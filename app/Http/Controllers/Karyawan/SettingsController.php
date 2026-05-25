<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\WajahKaryawan;
use App\Models\WajahRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
public function index()
{
    $user     = Auth::user();
    $karyawan = Karyawan::with(['departemen', 'jabatan'])
        ->where('user_id', $user->id)
        ->first(); // tidak pakai firstOrFail

    // Kalau tidak ada data karyawan (admin murni)
    if (!$karyawan) {
        return view('settings.index', [
            'karyawan'      => null,
            'wajahKaryawan' => null,
            'wajahRequest'  => null,
        ]);
    }

    $wajahKaryawan = WajahKaryawan::where('karyawan_id', $karyawan->id)->first();
    $wajahRequest  = WajahRequest::where('karyawan_id', $karyawan->id)->latest()->first();

    return view('settings.index', compact(
        'karyawan',
        'wajahKaryawan',
        'wajahRequest'
    ));
}

    /**
     * Update profile photo
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();

            if ($karyawan->foto_profil) {
                Storage::disk('public')->delete($karyawan->foto_profil);
            }

            $file     = $request->file('foto_profil');
            $fileName = 'profile_' . $karyawan->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('profiles', $fileName, 'public');

            $karyawan->update(['foto_profil' => $path]);

            return redirect()->route('settings.index')
                ->with('success', 'Foto profil berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengupdate foto profil: ' . $e->getMessage());
        }
    }

    /**
     * Update email
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'required'
        ]);

        try {
            $user = Auth::user();

            if (!Hash::check($request->password, $user->password)) {
                return redirect()->back()
                    ->with('error', 'Password yang Anda masukkan salah!');
            }

            $user->update(['email' => $request->email]);

            return redirect()->route('settings.index')
                ->with('success', 'Email berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengupdate email: ' . $e->getMessage());
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ],
        ], [
            'new_password.min'        => 'Password minimal 8 karakter',
            'new_password.mixed_case' => 'Password harus mengandung huruf besar dan kecil',
            'new_password.numbers'    => 'Password harus mengandung angka',
            'new_password.symbols'    => 'Password harus mengandung karakter khusus',
            'new_password.confirmed'  => 'Konfirmasi password tidak cocok',
        ]);

        try {
            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->with('error', 'Password saat ini salah!');
            }

            if (Hash::check($request->new_password, $user->password)) {
                return redirect()->back()
                    ->with('error', 'Password baru tidak boleh sama dengan password lama!');
            }

            $user->update(['password' => Hash::make($request->new_password)]);

            return redirect()->route('settings.index')
                ->with('success', 'Password berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengupdate password: ' . $e->getMessage());
        }
    }

    /**
     * Update personal data & identity
     */
    public function updatePersonalData(Request $request)
    {
        $request->validate([
            'no_telepon'          => 'nullable|string|max:20',
            'no_telepon_tambahan' => 'nullable|string|max:20',
            'tempat_lahir'        => 'nullable|string|max:100',
            'tanggal_lahir'       => 'nullable|date',
            'jenis_kelamin'       => 'nullable|in:laki-laki,perempuan',
            'status_pernikahan'   => 'nullable|in:belum_menikah,menikah,cerai',
            'golongan_darah'      => 'nullable|string|max:5',
            'agama'               => 'nullable|string|max:20',
            'nik'                 => 'nullable|string|max:16',
            'alamat_ktp'          => 'nullable|string|max:500',
            'kode_pos'            => 'nullable|string|max:10',
            'alamat_tinggal'      => 'nullable|string|max:500',
            'no_paspor'           => 'nullable|string|max:30',
            'masa_berlaku_paspor' => 'nullable|date',
        ]);

        try {
            $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();

            $karyawan->update($request->only([
                'no_telepon',
                'no_telepon_tambahan',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'status_pernikahan',
                'golongan_darah',
                'agama',
                'nik',
                'alamat_ktp',
                'kode_pos',
                'alamat_tinggal',
                'no_paspor',
                'masa_berlaku_paspor',
            ]));

            return redirect()->route('settings.index')
                ->with('success', 'Data pribadi berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
}
