<?php

namespace App\Http\Controllers\Karyawan;

use App\Models\WajahKaryawan;
use App\Models\WajahRequest;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class WajahRequestController extends Controller
{
    /**
     * Karyawan: ajukan permohonan ganti template wajah
     */
    public function store(Request $request)
    {
        $request->validate([
            'alasan' => 'required|string|min:10|max:500',
        ], [
            'alasan.required' => 'Alasan wajib diisi',
            'alasan.min'      => 'Alasan minimal 10 karakter',
        ]);

        $user     = Auth::user();
        $karyawan = $user->karyawan;

        // Cek apakah ada request pending yang belum diproses
        $pending = WajahRequest::where('karyawan_id', $karyawan->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            // return back()->with('error', 'Anda masih memiliki permohonan yang sedang diproses.');
            return back()->with('alert', [
                'type' => 'error',
                'title' => 'Permohonan wajah sudah diproses',
                'message' => 'Anda masih memiliki permohonan yang sedang diproses.'
            ]);
        }
        


        // Cek apakah sudah disetujui tapi belum capture
        $approved = WajahRequest::where('karyawan_id', $karyawan->id)
            ->where('status', 'disetujui')
            ->whereNull('captured_at')
            ->exists();

        if ($approved) {
            // return back()->with('error', 'Permohonan Anda sudah disetujui. Silakan lakukan capture wajah baru');
            
            return back()->with('alert', [
                'type' => 'error',
                'title' => 'Permohonan wajah sudah distujui',
                'message' => 'Permohonan Anda sudah disetujui. Silakan lakukan capture wajah baru.'
            ]);
        }

        WajahRequest::create([
            'karyawan_id' => $karyawan->id,
            'user_id'     => $user->id,
            'alasan'      => $request->alasan,
            'status'      => 'pending',
        ]);

        $adminUsers = User::whereIn('role', ['super_admin'])->get();
        // Notif ke super_admin
        foreach ($adminUsers as $admin) {
            Notifikasi::create([
                'user_id'     => $admin->id,  // ✅ diisi ID user
                'judul'       => 'Permohonan Ganti Template Wajah',
                'pesan'       => "{$user->nama} (" . optional($karyawan->departemen)->nama . ") mengajukan permohonan ganti template wajah.",
                'type'        => 'sistem',  
                'target_role' => $admin->role,
            ]);
        }

        activity_log('wajah', 'request', 'Mengajukan permohonan ganti template wajah');

        // return back()->with('success', 'Permohonan ganti wajah berhasil dikirim. Menunggu persetujuan admin.');
            return back()->with('alert', [
                'type' => 'success',
                'title' => 'Permohonan wajah sudah terkirim',
                'message' => 'Permohonan ganti wajah berhasil dikirim. Menunggu persetujuan admin.'
            ]);
    }

    /**
     * Karyawan: halaman capture wajah baru (setelah disetujui)
     */
    public function captureForm()
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        $wajahRequest = WajahRequest::where('karyawan_id', $karyawan->id)
            ->where('status', 'disetujui')
            ->whereNull('captured_at')
            ->latest()
            ->firstOrFail();

        return view('karyawan.wajah.capture', compact('wajahRequest'));
    }

    /**
     * Karyawan: simpan wajah baru setelah capture
     */
    public function captureStore(Request $request)
    {
        $request->validate([
            'face_encoding' => 'required|string',
            'face_image'    => 'required|string',
        ]);

        $user     = Auth::user();
        $karyawan = $user->karyawan;

        $wajahRequest = WajahRequest::where('karyawan_id', $karyawan->id)
            ->where('status', 'disetujui')
            ->whereNull('captured_at')
            ->latest()
            ->firstOrFail();

        // Update / replace wajah karyawan
        WajahKaryawan::updateOrCreate(
            ['karyawan_id' => $karyawan->id],
            [
                'face_encoding' => $request->face_encoding,
                'face_image'    => $request->face_image,
                'captured_by'   => $user->id,
            ]
        );

        // Tandai request sebagai selesai
        $wajahRequest->update(['captured_at' => now()]);

        activity_log('wajah', 'update', 'Berhasil memperbarui template wajah');

        Notifikasi::create([
            'user_id'     => $user->id,
            'judul'       => 'Template Wajah Diperbarui',
            'pesan'       => 'Template wajah Anda berhasil diperbarui.',
            'type'        => 'sistem',
            'target_role' => 'karyawan',
        ]);

        // return redirect()->route('settings.index')
        //     ->with('success', 'Template wajah berhasil diperbarui!');
            
            return redirect()->route('settings.index')->with('alert', [
                'type' => 'success',
                'title' => 'Template wajah diperbarui',
                'message' => 'Template wajah berhasil diperbarui!'
            ]);
    }
    
    
}
