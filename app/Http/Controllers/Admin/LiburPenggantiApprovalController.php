<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanLiburPengganti;
use App\Models\LiburPengganti;
use App\Models\LiburPenggantiApproval;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class LiburPenggantiApprovalController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;

        $query = PengajuanLiburPengganti::with(['karyawan.user', 'karyawan.departemen', 'approvals.approver'])
            ->where('status', 'pending');

        if ($userRole === 'admin' && Auth::user()->karyawan) {
            $departemenId = Auth::user()->karyawan->departemen_id;
            $query->where('current_step', 'admin')
                ->whereHas('karyawan', function($q) use ($departemenId) {
                    $q->where('departemen_id', $departemenId);
                });
        } elseif (in_array($userRole, ['gm', 'super_admin'])) {
            $query->where('current_step', $userRole);
        } else {
            $query->whereRaw('1=0');
        }

        $pengajuan = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.libur-pengganti.approval', compact('pengajuan'));
    }

    public function approve(Request $request, $id)
    {
        try {
            $pengajuan = PengajuanLiburPengganti::with(['karyawan', 'approvals'])->findOrFail($id);
            $userRole = Auth::user()->role;

            if ($pengajuan->status !== 'pending' || $pengajuan->current_step !== $userRole) {
                return back()->with('error', 'Anda tidak berhak menyetujui pengajuan ini.');
            }

            DB::beginTransaction();

            $currentApproval = $pengajuan->approvals()
                ->where('step', $userRole)
                ->where('status', 'pending')
                ->first();

            if (!$currentApproval) {
                throw new Exception('Step approval tidak ditemukan.');
            }

            $currentApproval->update([
                'status'      => 'disetujui',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            $nextApproval = $pengajuan->approvals()
                ->where('status', 'pending')
                ->where('id', '>', $currentApproval->id)
                ->orderBy('id')
                ->first();

            if ($nextApproval) {
                $pengajuan->update(['current_step' => $nextApproval->step]);

                Notifikasi::create([
                    'user_id' => $pengajuan->karyawan->user_id,
                    'judul'   => 'Libur Pengganti - Update',
                    'pesan'   => "Pengajuan libur pengganti Anda telah disetujui oleh " . strtoupper($userRole) . ". Menunggu persetujuan " . strtoupper($nextApproval->step) . ".",
                    'type'    => 'libur_pengganti',
                    'target_role' => 'karyawan',
                ]);

                // Notifikasi ke approver berikutnya
                $nextUsers = User::where('role', $nextApproval->step)->get();
                foreach ($nextUsers as $user) {
                    Notifikasi::create([
                        'user_id' => $user->id,
                        'judul'   => 'Pengajuan Libur Pengganti Menunggu',
                        'pesan'   => "Libur pengganti dari {$pengajuan->karyawan->user->nama} menunggu persetujuan Anda.",
                        'type'    => 'libur_pengganti',
                        'target_role' => $nextApproval->step,
                    ]);
                }

                DB::commit();
                return back()->with('success', "Disetujui. Menunggu approval dari " . strtoupper($nextApproval->step));
            } else {
                // Final approval
                $pengajuan->update([
                    'status'       => 'disetujui',
                    'current_step' => null,
                ]);

                // Kurangi saldo libur pengganti
                $saldo = LiburPengganti::where('karyawan_id', $pengajuan->karyawan_id)->first();
                if ($saldo) {
                    $saldo->decrement('saldo');
                    $saldo->update(['terakhir_diupdate' => now()]);
                }

                Notifikasi::create([
                    'user_id' => $pengajuan->karyawan->user_id,
                    'judul'   => 'Libur Pengganti Disetujui',
                    'pesan'   => "Pengajuan libur pengganti Anda tanggal {$pengajuan->tanggal->format('d/m/Y')} telah disetujui sepenuhnya.",
                    'type'    => 'libur_pengganti',
                    'target_role' => 'karyawan',
                ]);

                DB::commit();
                return back()->with('success', 'Pengajuan disetujui sepenuhnya.');
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error Approve Libur Pengganti: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'catatan_admin' => 'required|string|min:10',
            ]);

            $pengajuan = PengajuanLiburPengganti::with('karyawan')->findOrFail($id);
            $userRole = Auth::user()->role;

            if ($pengajuan->status !== 'pending' || $pengajuan->current_step !== $userRole) {
                return back()->with('error', 'Anda tidak berhak menolak pengajuan ini.');
            }

            DB::beginTransaction();

            $currentApproval = $pengajuan->approvals()
                ->where('step', $userRole)
                ->where('status', 'pending')
                ->first();

            if (!$currentApproval) {
                throw new Exception('Step approval tidak ditemukan.');
            }

            $currentApproval->update([
                'status'      => 'ditolak',
                'approved_by' => Auth::id(),
                'catatan'     => $request->catatan_admin,
                'approved_at' => now(),
            ]);

            // Tolak semua step berikutnya
            $pengajuan->approvals()
                ->where('id', '>', $currentApproval->id)
                ->update(['status' => 'ditolak']);

            $pengajuan->update([
                'status'        => 'ditolak',
                'current_step'  => null,
                'catatan_admin' => $request->catatan_admin,
            ]);

            Notifikasi::create([
                'user_id' => $pengajuan->karyawan->user_id,
                'judul'   => 'Libur Pengganti Ditolak',
                'pesan'   => "Pengajuan libur pengganti Anda tanggal {$pengajuan->tanggal->format('d/m/Y')} ditolak.\n\nAlasan: {$request->catatan_admin}",
                'type'    => 'libur_pengganti',
                'target_role' => 'karyawan',
            ]);

            DB::commit();
            return back()->with('success', 'Pengajuan berhasil ditolak.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error Reject Libur Pengganti: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
        public function detail($id)
    {
        $pengajuan = PengajuanLiburPengganti::with([
            'karyawan.user',
            'karyawan.departemen',
            'karyawan.jabatan',
            'approvals'
        ])->findOrFail($id);
    
        return view('admin.libur-pengganti.detail', compact('pengajuan'));
    }
}