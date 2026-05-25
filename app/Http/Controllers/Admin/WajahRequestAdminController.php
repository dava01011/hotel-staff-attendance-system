<?php

namespace App\Http\Controllers\Admin;

use App\Models\WajahRequest;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class WajahRequestAdminController extends Controller
{
    /**
     * Check if user can approve/reject wajah requests
     * Only Super Admin allowed
     */
    private function canApproveWajah()
    {
        return Auth::user()->role === 'super_admin';
    }

    public function index()
    {
        // Check authorization
        if (!$this->canApproveWajah()) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola permohonan wajah.');
        }

        $requests = WajahRequest::with(['karyawan.user', 'karyawan.departemen', 'reviewer'])
            ->orderByRaw("FIELD(status, 'pending', 'disetujui', 'ditolak')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $pendingCount = WajahRequest::where('status', 'pending')->count();
        $userRole = Auth::user()->role;

        return view('admin.wajah.requests', compact('requests', 'pendingCount', 'userRole'));
    }

    public function approve(Request $request, $id)
    {
        // Check authorization
        if (!$this->canApproveWajah()) {
            abort(403, 'Anda tidak memiliki akses untuk menyetujui permohonan wajah.');
        }

        $wajahRequest = WajahRequest::with(['karyawan.user'])->findOrFail($id);

        if ($wajahRequest->status !== 'pending') {
            return back()->with('error', 'Permohonan ini sudah diproses.');
        }

        $wajahRequest->update([
            'status'      => 'disetujui',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $nama = $wajahRequest->karyawan->user->nama;

        Notifikasi::create([
            'user_id'     => $wajahRequest->user_id,
            'judul'       => 'Permohonan Wajah Disetujui',
            'pesan'       => 'Permohonan ganti template wajah Anda telah disetujui. Silakan lakukan capture wajah baru di halaman Settings.',
            'type'        => 'sistem',
            'target_role' => 'karyawan',
        ]);

        activity_log('wajah', 'approve', "Menyetujui permohonan ganti wajah dari {$nama}");

        // return back()->with('success', "Permohonan {$nama} berhasil disetujui.");
        
                    return back()->with('alert', [
                'type' => 'success',
                'title' => 'Permohonan wajah diperbarui',
                'message' => 'Permohonan {$nama} berhasil disetujui.'
            ]);
    }

    public function reject(Request $request, $id)
    {
        // Check authorization
        if (!$this->canApproveWajah()) {
            abort(403, 'Anda tidak memiliki akses untuk menolak permohonan wajah.');
        }

        $request->validate([
            'catatan_admin' => 'required|string|min:5',
        ], [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi',
            'catatan_admin.min'      => 'Alasan minimal 5 karakter',
        ]);

        $wajahRequest = WajahRequest::with(['karyawan.user'])->findOrFail($id);

        if ($wajahRequest->status !== 'pending') {
            return back()->with('error', 'Permohonan ini sudah diproses.');
        }

        $wajahRequest->update([
            'status'        => 'ditolak',
            'reviewed_by'   => Auth::id(),
            'reviewed_at'   => now(),
            'catatan_admin' => $request->catatan_admin,
        ]);

        $nama = $wajahRequest->karyawan->user->nama;

        Notifikasi::create([
            'user_id'     => $wajahRequest->user_id,
            'judul'       => 'Permohonan Wajah Ditolak',
            'pesan'       => "Permohonan ganti template wajah Anda ditolak.\n\nAlasan: {$request->catatan_admin}",
            'type'        => 'wajah',
            'target_role' => 'karyawan',
        ]);

        activity_log('wajah', 'reject', "Menolak permohonan ganti wajah dari {$nama}");

        return back()->with('success', "Permohonan {$nama} berhasil ditolak.");
    }
}