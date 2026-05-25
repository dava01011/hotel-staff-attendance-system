<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AjukanShift;
use App\Models\Cuti;
use App\Models\CutiApproval;
use App\Models\KaryawanShiftPattern;
use App\Models\JatahCuti;
use App\Models\Karyawan;
use App\Models\LiburPengganti;
use App\Models\LiburPenggantiApproval;
use App\Models\Notifikasi;
use App\Models\PengajuanLiburPengganti;
use App\Models\User;
use App\Models\WajahRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\CutiApprovedMail;
use App\Mail\CutiRejectedMail;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    /**
     * Display unified approval page with Cuti + Libur Pengganti
     * GET /admin/approval
     */
public function index()
{
    $user = Auth::user();
    $userRole = $user->role;
    $karyawan = $user->karyawan;
    $departemenId = $karyawan ? $karyawan->departemen_id : null;

    // ========================================
    // ===== 1. CUTI =====
    // ========================================
    $cutiQuery = Cuti::with([
        'karyawan.user',
        'karyawan.departemen',
        'jenisCuti',
        'approvals.approver'
    ])->where('status', 'pending');

    if ($userRole === 'admin' && $departemenId) {
        $cutiQuery->where('current_step', 'admin')
            ->whereHas('karyawan', function($q) use ($departemenId) {
                $q->where('departemen_id', $departemenId);
            });
    } elseif (in_array($userRole, ['gm', 'super_admin'])) {
        $cutiQuery->where('current_step', $userRole);
    } else {
        $cutiQuery->whereRaw('1=0');
    }

    $cuti = $cutiQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'cuti_page');
    $cutiCount = ['pending' => $cuti->total()];

    // ========================================
    // ===== 2. LIBUR PENGGANTI =====
    // ========================================
    $liburQuery = PengajuanLiburPengganti::with([
        'karyawan.user',
        'karyawan.departemen',
        'approvals'
    ])->where('status', 'pending');

    if ($userRole === 'admin' && $departemenId) {
        $liburQuery->where('current_step', 'admin')
            ->whereHas('karyawan', function($q) use ($departemenId) {
                $q->where('departemen_id', $departemenId);
            });
    } elseif (in_array($userRole, ['gm', 'super_admin'])) {
        $liburQuery->where('current_step', $userRole);
    } else {
        $liburQuery->whereRaw('1=0');
    }

    $liburPengganti = $liburQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'libur_page');
    $liburCount = ['pending' => $liburPengganti->total()];

    // ========================================
    // ===== 3. WAJAH REQUESTS =====
    // ========================================
    $wajahRequests = null;
    $wajahCount = ['pending' => 0];

    if ($userRole === 'super_admin') {
        $wajahRequests = WajahRequest::with(['karyawan.user', 'karyawan.departemen'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'wajah_page');
        $wajahCount = ['pending' => $wajahRequests->total()];
    } else {
        $wajahRequests = WajahRequest::whereRaw('1=0')->paginate(15, ['*'], 'wajah_page');
    }

    // ========================================
    // ===== 4. SHIFT REQUESTS =====
    // ========================================
    $shift = null;
    $shiftCount = ['pending' => 0];

    if (in_array($userRole, ['super_admin', 'admin', 'gm'])) {
        $shiftQuery = AjukanShift::with([
            'departemen',
            'shiftLama',
            'shiftBaru',
            'pemohon',
            'approvals.approver'
        ])->where('status', 'pending');

        if ($userRole === 'admin' && $departemenId) {
            $shiftQuery->where('current_step', 'admin')
                ->where('departemen_id', $departemenId);
        } elseif (in_array($userRole, ['super_admin', 'gm'])) {
            $shiftQuery->where('current_step', $userRole);
        }

        $shift = $shiftQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'shift_page');
        $shiftCount = ['pending' => $shift->total()];
    } else {
        $shift = AjukanShift::whereRaw('1=0')->paginate(15, ['*'], 'shift_page');
    }

    // ========================================
    // ===== 5. STATS (APPROVALS TODAY) =====
    // ========================================
    $approvedToday = CutiApproval::where('approved_by', Auth::id())
        ->where('status', 'disetujui')
        ->whereDate('approved_at', today())
        ->count();

    $rejectedToday = CutiApproval::where('approved_by', Auth::id())
        ->where('status', 'ditolak')
        ->whereDate('approved_at', today())
        ->count();

    $shiftApprovedToday = AjukanShift::where('approved_by', Auth::id())
        ->where('status', 'disetujui')
        ->whereDate('updated_at', today())
        ->count();

    $shiftRejectedToday = AjukanShift::where('approved_by', Auth::id())
        ->where('status', 'ditolak')
        ->whereDate('updated_at', today())
        ->count();

    return view('admin.approval.index', compact(
        'cuti',
        'cutiCount',
        'liburPengganti',
        'liburCount',
        'wajahRequests',
        'wajahCount',
        'shift',
        'shiftCount',
        'approvedToday',
        'rejectedToday',
        'shiftApprovedToday',
        'shiftRejectedToday'
    ));
}

    // ========================================
    // ===== CUTI APPROVAL METHODS =====
    // ========================================

    /**
     * Approve Cuti
     * POST /admin/cuti/{id}/approve
     */
    public function approveCuti(Request $request, $id)
    {
        try {
            $cuti = Cuti::with(['karyawan', 'jenisCuti', 'approvals'])->findOrFail($id);
            $userRole = Auth::user()->role;

            if ($cuti->status !== 'pending') {
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Gagal!',
                    'message' => 'Cuti ini sudah diproses sebelumnya'
                ]);
            }

            if ($cuti->current_step !== $userRole) {
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Tidak Diizinkan!',
                    'message' => 'Anda tidak berhak menyetujui cuti ini pada tahap saat ini'
                ]);
            }

            if ($cuti->karyawan->user_id === Auth::id()) {
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Tidak Diizinkan!',
                    'message' => 'Anda tidak dapat menyetujui cuti Anda sendiri'
                ]);
            }

            DB::beginTransaction();

            $currentApproval = $cuti->approvals()
                ->where('step', $userRole)
                ->where('status', 'pending')
                ->first();

            if (!$currentApproval) {
                DB::rollBack();
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Gagal!',
                    'message' => 'Step approval tidak ditemukan atau sudah diproses'
                ]);
            }

            $currentApproval->update([
                'status' => 'disetujui',
                'approved_by' => Auth::id(),
                'catatan' => $request->catatan_admin ?? null,
                'approved_at' => now()
            ]);

            if (function_exists('activity_log')) {
                activity_log(
                    'cuti',
                    'approve',
                    "Menyetujui cuti {$cuti->jenisCuti->nama} karyawan {$cuti->karyawan->user->nama} sebagai {$userRole}"
                );
            }

            $nextApproval = CutiApproval::where('cuti_id', $cuti->id)
                ->where('status', 'pending')
                ->where('id', '>', $currentApproval->id)
                ->orderBy('id', 'asc')
                ->first();

            if ($nextApproval) {
                $nextApproverUser = $nextApproval->getApproverUser();
                $email = $nextApproverUser->email ?? null;

                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->queue(
                        new CutiApprovedMail($cuti, $userRole, $nextApproval->step, $nextApproverUser->nama)
                    );
                }

                $cuti->update(['current_step' => $nextApproval->step]);

                Notifikasi::create([
                    'user_id' => $cuti->karyawan->user_id,
                    'judul' => 'Persetujuan Cuti - Update',
                    'pesan' => "Pengajuan cuti {$cuti->jenisCuti->nama} Anda telah disetujui oleh " . strtoupper($userRole) . ". Menunggu persetujuan " . strtoupper($nextApproval->step) . ".",
                    'type' => 'cuti',
                    'target_role' => 'karyawan'
                ]);

                $nextUsers = User::where('role', $nextApproval->step)->get();
                foreach ($nextUsers as $user) {
                    Notifikasi::create([
                        'user_id' => $user->id,
                        'judul' => 'Pengajuan Cuti Menunggu Persetujuan',
                        'pesan' => "Cuti {$cuti->jenisCuti->nama} dari {$cuti->karyawan->user->nama} menunggu persetujuan Anda.",
                        'type' => 'cuti',
                        'target_role' => $nextApproval->step
                    ]);
                }

                DB::commit();
                return back()->with('alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'message' => "Cuti disetujui. Menunggu approval dari " . strtoupper($nextApproval->step)
                ]);
            } else {
                $this->finalizeCutiApproval($cuti, $userRole);

                $email = $cuti->karyawan->user->email ?? null;
                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->queue(new CutiApprovedMail($cuti, $userRole, null, null));
                }

                DB::commit();
                return back()->with('alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'message' => 'Cuti berhasil disetujui sepenuhnya'
                ]);
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error Approve Cuti: ' . $e->getMessage());
            return back()->with('alert', [
                'type' => 'error',
                'title' => 'Gagal!',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reject Cuti
     * POST /admin/cuti/{id}/reject
     */
    public function rejectCuti(Request $request, $id)
    {
        try {
            $request->validate([
                'catatan_admin' => 'required|string|min:10'
            ]);

            $cuti = Cuti::with(['karyawan', 'jenisCuti', 'approvals'])->findOrFail($id);
            $userRole = Auth::user()->role;

            if ($cuti->status !== 'pending') {
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Gagal!',
                    'message' => 'Cuti ini sudah diproses sebelumnya'
                ]);
            }

            if ($cuti->current_step !== $userRole) {
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Tidak Diizinkan!',
                    'message' => 'Anda tidak berhak menolak cuti ini pada tahap saat ini'
                ]);
            }

            if ($cuti->karyawan->user_id === Auth::id()) {
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Tidak Diizinkan!',
                    'message' => 'Anda tidak dapat menolak cuti Anda sendiri'
                ]);
            }

            DB::beginTransaction();

            $currentApproval = $cuti->approvals()
                ->where('step', $userRole)
                ->where('status', 'pending')
                ->first();

            if (!$currentApproval) {
                DB::rollBack();
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Gagal!',
                    'message' => 'Step approval tidak ditemukan atau sudah diproses'
                ]);
            }

            $currentApproval->update([
                'status' => 'ditolak',
                'approved_by' => Auth::id(),
                'catatan' => $request->catatan_admin,
                'approved_at' => now()
            ]);

            CutiApproval::where('cuti_id', $cuti->id)
                ->where('id', '>', $currentApproval->id)
                ->update(['status' => 'ditolak']);

            $cuti->update([
                'status' => 'ditolak',
                'current_step' => null,
                'catatan_admin' => $request->catatan_admin
            ]);

            if (function_exists('activity_log')) {
                activity_log(
                    'cuti',
                    'reject',
                    "Menolak cuti {$cuti->jenisCuti->nama} karyawan {$cuti->karyawan->user->nama} sebagai {$userRole}"
                );
            }

            $pesan = "Pengajuan cuti {$cuti->jenisCuti->nama} Anda ditolak oleh " . strtoupper($userRole) . ".\n\nAlasan: {$request->catatan_admin}";

            Notifikasi::create([
                'user_id' => $cuti->karyawan->user_id,
                'judul' => 'Cuti Ditolak',
                'pesan' => $pesan,
                'type' => 'cuti',
                'target_role' => 'karyawan'
            ]);

            DB::commit();

            $email = $cuti->karyawan->user->email ?? null;
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($email)->queue(new CutiRejectedMail($cuti, $request->catatan_admin, $userRole));
            }

            return back()->with('alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'message' => 'Pengajuan cuti berhasil ditolak'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error Reject Cuti: ' . $e->getMessage());
            return back()->with('alert', [
                'type' => 'error',
                'title' => 'Gagal!',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show detail cuti
     * GET /admin/approval/cuti/{id}
     */
    public function detailCuti($id)
    {
        $cuti = Cuti::with([
            'karyawan.user',
            'karyawan.departemen',
            'karyawan.jabatan',
            'jenisCuti',
            'approvals.approver'
        ])->findOrFail($id);

        return view('admin.approval.cuti-detail', compact('cuti'));
    }

    /**
     * Show detail shift
     * GET /admin/approval/shift/{id}
     */
    public function detailShift($id)
    {
        $ajukanShift = AjukanShift::with([
            'departemen',
            'shiftLama',
            'shiftBaru',
            'pemohon.karyawan',
            'approver'
        ])->findOrFail($id);

        $user = Auth::user();
        if (!in_array($user->role, ['super_admin', 'admin', 'gm'])) {
            abort(403, 'Anda tidak berhak mengakses halaman ini');
        }

        // Jika dia admin departemen, pastikan hanya bisa lihat departemennya (opsional, tapi lebih aman)
        if ($user->role === 'admin' && $ajukanShift->departemen_id !== $user->karyawan->departemen_id) {
             abort(403, 'Anda tidak berhak mengakses pengajuan dari departemen lain');
        }

        $jumlahKaryawan = Karyawan::where('departemen_id', $ajukanShift->departemen_id)->count();

        $jadwalAktif = KaryawanShiftPattern::where('karyawan_id', $ajukanShift->requested_by)
            ->where('is_default', true)
            ->where('hari', 'senin') // Ambil contoh senin untuk referensi
            ->with('shift')
            ->first();

        return view('admin.approval.shift-detail', compact('ajukanShift', 'jumlahKaryawan', 'jadwalAktif'));
    }

    // ======================================
    // ===== SHIFT APPROVAL METHODS =====
    // ======================================

    /**
     * Approve Shift (Multi-level)
     */
    public function approveShift(Request $request, $id)
    {
        try {
            $ajukanShift = AjukanShift::with(['departemen', 'approvals'])->findOrFail($id);
            $userRole = Auth::user()->role;

            if ($ajukanShift->status !== 'pending') {
                return back()->with('alert', [
                    'type' => 'error', 'title' => 'Gagal!', 'message' => 'Pengajuan shift ini sudah diproses sebelumnya'
                ]);
            }

            if ($ajukanShift->current_step !== $userRole) {
                return back()->with('alert', [
                    'type' => 'error', 'title' => 'Tidak Diizinkan!', 'message' => 'Anda tidak berhak menyetujui pada tahap ini'
                ]);
            }

            DB::beginTransaction();

            $currentApproval = $ajukanShift->approvals()
                ->where('step', $userRole)
                ->where('status', 'pending')
                ->first();

            if (!$currentApproval) {
                DB::rollBack();
                return back()->with('alert', [
                    'type' => 'error', 'title' => 'Gagal!', 'message' => 'Step approval tidak ditemukan'
                ]);
            }

            $currentApproval->update([
                'status'      => 'disetujui',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Cari approval berikutnya
            $nextApproval = $ajukanShift->approvals()
                ->where('status', 'pending')
                ->where('id', '>', $currentApproval->id)
                ->orderBy('id')
                ->first();

            if ($nextApproval) {
                $ajukanShift->update(['current_step' => $nextApproval->step]);

                Notifikasi::create([
                    'user_id'     => $ajukanShift->requested_by,
                    'judul'       => 'Shift - Update',
                    'pesan'       => "Pengajuan shift Anda telah disetujui oleh " . strtoupper($userRole) . ". Menunggu persetujuan " . strtoupper($nextApproval->step) . ".",
                    'type'        => 'shift',
                    'target_role' => 'karyawan'
                ]);

                $nextUsers = User::where('role', $nextApproval->step)->get();
                foreach ($nextUsers as $user) {
                    Notifikasi::create([
                        'user_id'     => $user->id,
                        'judul'       => 'Pengajuan Shift Menunggu',
                        'pesan'       => "Shift dari {$ajukanShift->pemohon->nama} menunggu persetujuan Anda.",
                        'type'        => 'shift',
                        'target_role' => $nextApproval->step
                    ]);
                }

                DB::commit();
                return back()->with('alert', [
                    'type' => 'success', 'title' => 'Berhasil!',
                    'message' => "Disetujui. Menunggu approval dari " . strtoupper($nextApproval->step)
                ]);
            } else {
                // Final approval - terapkan perubahan shift
                $this->applyShiftChange($ajukanShift);
                $ajukanShift->update([
                    'status'       => 'disetujui',
                    'current_step' => null,
                ]);

                Notifikasi::create([
                    'user_id'     => $ajukanShift->requested_by,
                    'judul'       => 'Shift Disetujui',
                    'pesan'       => "Pengajuan pergantian shift Anda telah disetujui sepenuhnya.",
                    'type'        => 'shift',
                    'target_role' => 'karyawan'
                ]);

                DB::commit();
                return back()->with('alert', [
                    'type' => 'success', 'title' => 'Berhasil!', 'message' => 'Pengajuan shift disetujui sepenuhnya.'
                ]);
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error Approve Shift: ' . $e->getMessage());
            return back()->with('alert', [
                'type' => 'error', 'title' => 'Gagal!', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reject Shift (Multi-level)
     */
    public function rejectShift(Request $request, $id)
    {
        try {
            $request->validate(['catatan_admin' => 'required|string|min:10']);

            $ajukanShift = AjukanShift::with('approvals')->findOrFail($id);
            $userRole = Auth::user()->role;

            if ($ajukanShift->status !== 'pending' || $ajukanShift->current_step !== $userRole) {
                return back()->with('alert', [
                    'type' => 'error', 'title' => 'Gagal!', 'message' => 'Anda tidak berhak menolak pengajuan ini.'
                ]);
            }

            DB::beginTransaction();

            $currentApproval = $ajukanShift->approvals()
                ->where('step', $userRole)
                ->where('status', 'pending')
                ->first();

            if (!$currentApproval) {
                DB::rollBack();
                return back()->with('alert', [
                    'type' => 'error', 'title' => 'Gagal!', 'message' => 'Step approval tidak ditemukan'
                ]);
            }

            $currentApproval->update([
                'status'      => 'ditolak',
                'approved_by' => Auth::id(),
                'catatan'     => $request->catatan_admin,
                'approved_at' => now(),
            ]);

            // Tolak semua step berikutnya
            $ajukanShift->approvals()
                ->where('id', '>', $currentApproval->id)
                ->update(['status' => 'ditolak']);

            $ajukanShift->update([
                'status'        => 'ditolak',
                'current_step'  => null,
                'catatan_admin' => $request->catatan_admin,
            ]);

            Notifikasi::create([
                'user_id'     => $ajukanShift->requested_by,
                'judul'       => 'Shift Ditolak',
                'pesan'       => "Pengajuan shift Anda ditolak.\n\nAlasan: {$request->catatan_admin}",
                'type'        => 'shift',
                'target_role' => 'karyawan'
            ]);

            DB::commit();
            return back()->with('alert', [
                'type' => 'success', 'title' => 'Berhasil!', 'message' => 'Pengajuan shift berhasil ditolak'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error Reject Shift: ' . $e->getMessage());
            return back()->with('alert', [
                'type' => 'error', 'title' => 'Gagal!', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Terapkan perubahan shift (final approval)
     */
    private function applyShiftChange($ajukanShift)
    {
        $tanggalMulai = Carbon::parse($ajukanShift->tanggal_mulai);
        $tanggalSelesai = $ajukanShift->tanggal_selesai ? Carbon::parse($ajukanShift->tanggal_selesai) : null;
        $departemenId = $ajukanShift->departemen_id;
        $shiftBaruId = $ajukanShift->shift_baru_id;

        $karyawans = Karyawan::where('departemen_id', $departemenId)->get();

        foreach ($karyawans as $k) {
            if ($ajukanShift->jenis === 'permanen') {
                // Update DEFAULT pattern untuk semua hari yang bertipe 'kerja'
                KaryawanShiftPattern::where('karyawan_id', $k->id)
                    ->where('is_default', true)
                    ->where('tipe', 'kerja')
                    ->update(['shift_id' => $shiftBaruId]);
            } else {
                // Update WEEKLY override untuk periode tersebut
                $current = $tanggalMulai->copy();
                while ($current <= $tanggalSelesai) {
                    $mingguKe = $current->weekOfYear;
                    $tahun = $current->year;
                    $hari = KaryawanShiftPattern::getNamaHari($current);

                    // Ambil tipe dari default pattern untuk hari tersebut
                    $defaultPattern = KaryawanShiftPattern::where('karyawan_id', $k->id)
                        ->where('is_default', true)
                        ->where('hari', $hari)
                        ->first();
                    
                    $tipe = $defaultPattern ? $defaultPattern->tipe : 'kerja';

                    KaryawanShiftPattern::updateOrCreate([
                        'karyawan_id' => $k->id,
                        'hari' => $hari,
                        'minggu_ke' => $mingguKe,
                        'tahun' => $tahun,
                        'is_default' => false,
                    ], [
                        'shift_id' => ($tipe === 'kerja') ? $shiftBaruId : null,
                        'tipe' => $tipe,
                        'is_active' => true,
                    ]);

                    $current->addDay();
                }
            }
        }
    }

    // ======================================
    // ===== HELPER METHODS =====
    // ======================================

    /**
     * Finalize Cuti Approval
     */
    private function finalizeCutiApproval($cuti, $userRole)
    {
        $mulai = Carbon::parse($cuti->tanggal_mulai)->startOfDay();
        $selesai = Carbon::parse($cuti->tanggal_selesai)->startOfDay();
        $jumlahHari = $mulai->diffInDays($selesai) + 1;

        $jatah = JatahCuti::where('karyawan_id', $cuti->karyawan_id)
            ->where('tahun', now()->year)
            ->first();

        if (!$jatah || $jatah->jatah < $jumlahHari) {
            throw new Exception('Jatah cuti karyawan tidak mencukupi');
        }

        $jatah->decrement('jatah', $jumlahHari);

        $cuti->update([
            'status' => 'disetujui',
            'current_step' => null,
            'catatan_admin' => null
        ]);

        $tanggal = Carbon::parse($cuti->tanggal_mulai);
        while ($tanggal->lte($cuti->tanggal_selesai)) {
            Absensi::updateOrCreate([
                'karyawan_id' => $cuti->karyawan_id,
                'tanggal' => $tanggal->toDateString(),
            ], [
                'status' => 'cuti',
            ]);
            $tanggal->addDay();
        }

        if (function_exists('activity_log')) {
            activity_log(
                'cuti',
                'approve',
                "Menyetujui final cuti {$cuti->jenisCuti->nama} karyawan {$cuti->karyawan->user->nama} sebagai {$userRole}"
            );
        }

        Notifikasi::create([
            'user_id' => $cuti->karyawan->user_id,
            'judul' => 'Cuti Disetujui',
            'pesan' => "Pengajuan cuti {$cuti->jenisCuti->nama} Anda telah disetujui sepenuhnya untuk periode {$cuti->tanggal_mulai->format('d/m/Y')} - {$cuti->tanggal_selesai->format('d/m/Y')}.",
            'type' => 'cuti',
            'target_role' => 'karyawan'
        ]);
    }
}