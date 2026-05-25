<?php

namespace App\Http\Controllers\Karyawan;

use Exception;
use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Notifikasi;
use App\Models\AjukanShift;
use App\Models\KaryawanShiftPattern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AjukanShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $adminRoles = ['admin', 'super_admin'];
            
            if (!in_array($user->role, $adminRoles)) {
                return redirect()->route('karyawan.dashboard')->with('alert', [
                    'type' => 'error',
                    'title' => 'Pengajuan Shift',
                    'message' => 'Anda tidak memiliki akses ke fitur ini.'
                ]);
            }

            if (!$user->karyawan || !$user->karyawan->departemen_id) {
                return redirect()->route('karyawan.dashboard')->with('alert', [
                    'type' => 'error',
                    'title' => 'Pengajuan Shift',
                    'message' => 'Data departemen tidak ditemukan.'
                ]);
            }

            return $next($request);
        });
    }

    /**
     * Display index page (tidak digunakan langsung karena sudah ada PengajuanController@index)
     * Method ini tetap dipertahankan untuk fallback
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        $departemen = $karyawan->departemen;

        $shiftAktif = AjukanShift::with(['shiftLama', 'shiftBaru', 'approvals'])
            ->where('departemen_id', $departemen->id)
            ->where('requested_by', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $shiftSelesai = AjukanShift::with(['shiftLama', 'shiftBaru', 'approvals'])
            ->where('departemen_id', $departemen->id)
            ->where('requested_by', $user->id)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->latest()
            ->take(3)
            ->get();

        $jadwalShiftAktif = KaryawanShiftPattern::where('karyawan_id', $karyawan->id)
            ->where('is_default', true)
            ->where('hari', 'senin')
            ->with('shift')
            ->first();

        return view('karyawan.pengajuan.index', compact(
            'shiftAktif',
            'shiftSelesai',
            'jadwalShiftAktif'
        ));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        $departemen = $karyawan->departemen;

        $hasPending = AjukanShift::where('departemen_id', $departemen->id)
            ->where('requested_by', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return redirect()->route('karyawan.ajukan-shift.index')
                ->with('error', 'Anda masih memiliki pengajuan shift yang belum diproses.');
        }

        $jadwalShiftAktif = KaryawanShiftPattern::where('karyawan_id', $karyawan->id)
            ->where('is_default', true)
            ->where('hari', 'senin')
            ->with('shift')
            ->first();

        if (!$jadwalShiftAktif) {
            return redirect()->route('karyawan.ajukan-shift.index')
                ->with('error', 'Shift departemen Anda belum ditentukan.');
        }

        $allShifts = Shift::where('id', '!=', $jadwalShiftAktif->shift_id)->get();

        return view('karyawan.ajukan-shift.create', compact(
            'departemen',
            'jadwalShiftAktif',
            'allShifts'
        ));
    }

    /**
     * Store new shift request
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'jenis'           => 'required|in:sementara,permanen',
                'shift_baru_id'   => 'required|exists:shift,id',
                'tanggal_mulai'   => 'required|date|after_or_equal:today',
                'tanggal_selesai' => 'required_if:jenis,sementara|nullable|date|after_or_equal:tanggal_mulai',
                'alasan'          => 'required|string|min:10|max:500'
            ]);

            $user = Auth::user();
            $karyawan = $user->karyawan;
            $departemen = $karyawan->departemen;

            $pendingExists = AjukanShift::where('departemen_id', $departemen->id)
                ->where('requested_by', $user->id)
                ->where('status', 'pending')
                ->exists();

            if ($pendingExists) {
                return back()->with('error', 'Masih ada pengajuan shift yang belum diproses.');
            }

            $jadwalShiftAktif = KaryawanShiftPattern::where('karyawan_id', $karyawan->id)
                ->where('is_default', true)
                ->where('hari', 'senin')
                ->firstOrFail();

            $shiftLamaId = $jadwalShiftAktif->shift_id;
            $shiftBaruId = $request->shift_baru_id;

            if ($shiftLamaId == $shiftBaruId) {
                return back()->with('error', 'Shift pengganti tidak boleh sama dengan shift saat ini!');
            }

            $tanggalSelesai = $request->jenis == 'permanen' ? null : $request->tanggal_selesai;

            if ($request->jenis == 'sementara') {
                $overlap = AjukanShift::where('departemen_id', $departemen->id)
                    ->where('jenis', 'sementara')
                    ->where('status', 'disetujui')
                    ->where(function($query) use ($request) {
                        $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai])
                            ->orWhereBetween('tanggal_selesai', [$request->tanggal_mulai, $request->tanggal_selesai])
                            ->orWhere(function($q) use ($request) {
                                $q->where('tanggal_mulai', '<=', $request->tanggal_mulai)
                                  ->where('tanggal_selesai', '>=', $request->tanggal_selesai);
                            });
                    })
                    ->exists();

                if ($overlap) {
                    return back()->with('error', 'Sudah ada pengajuan shift yang disetujui pada periode tersebut!');
                }
            }

            DB::beginTransaction();

            $ajukanShift = AjukanShift::create([
                'departemen_id'  => $departemen->id,
                'shift_lama_id'  => $shiftLamaId,
                'shift_baru_id'  => $shiftBaruId,
                'tanggal_mulai'  => $request->tanggal_mulai,
                'tanggal_selesai'=> $tanggalSelesai,
                'jenis'          => $request->jenis,
                'requested_by'   => $user->id,
                'alasan'         => $request->alasan,
                'status'         => 'pending'
            ]);

            // ✅ BUAT APPROVAL STEPS
            $ajukanShift->createApprovalSteps($user->role);

            activity_log('shift', 'create', "Mengajukan pergantian shift {$request->jenis} untuk departemen {$departemen->nama}");

            Notifikasi::create([
                'user_id'     => $user->id,
                'judul'       => 'Pengajuan Shift Berhasil',
                'pesan'       => "Pengajuan pergantian shift {$request->jenis} Anda berhasil dikirim dan menunggu persetujuan.",
                'type'        => 'shift',
                'target_role' => 'karyawan'
            ]);

            // Notifikasi ke approver pertama
            $firstStep = $ajukanShift->current_step;
            if ($firstStep) {
                Notifikasi::create([
                    'user_id'     => $user->id,
                    'judul'       => 'Pengajuan Shift Baru',
                    'pesan'       => "{$user->nama} mengajukan pergantian shift {$request->jenis} untuk departemen {$departemen->nama}.",
                    'type'        => 'shift',
                    'target_role' => $firstStep
                ]);
            }

            DB::commit();

            $jenisText = $request->jenis == 'sementara' ? 'sementara' : 'permanen';
            return redirect()->route('karyawan.ajukan-shift.index')->with('alert', [
                'type'    => 'success',
                'title'   => 'Pengajuan Shift',
                'message' => "Pengajuan shift {$jenisText} berhasil dikirim!"
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error Ajukan Shift: ' . $e->getMessage());
            return back()->with('alert', [
                'type'    => 'error',
                'title'   => 'Gagal!',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show riwayat page
     */
    public function riwayat()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $pengajuan = AjukanShift::with(['shiftLama', 'shiftBaru', 'approvals'])
            ->where('departemen_id', $karyawan->departemen_id)
            ->where('requested_by', $user->id)
            ->latest()
            ->paginate(20);

        return view('karyawan.ajukan-shift.riwayat', compact('pengajuan'));
    }

    /**
     * Show detail pengajuan
     */
    public function show($id)
    {
        $user = Auth::user();

        $ajukan = AjukanShift::with(['shiftLama', 'shiftBaru', 'approvals.approver'])
            ->where('requested_by', $user->id)
            ->findOrFail($id);

        return view('karyawan.ajukan-shift.detail', compact('ajukan'));
    }

    /**
     * Cancel pengajuan (hanya yang masih pending)
     */
    public function cancel($id)
    {
        try {
            $user = Auth::user();

            $ajukan = AjukanShift::where('id', $id)
                ->where('requested_by', $user->id)
                ->where('status', 'pending')
                ->firstOrFail();

            DB::transaction(function () use ($ajukan) {
                $ajukan->update([
                    'status'        => 'ditolak',
                    'catatan_admin' => 'Dibatalkan oleh pemohon'
                ]);
                $ajukan->approvals()->update(['status' => 'ditolak']);
            });

            return back()->with('success', 'Pengajuan shift berhasil dibatalkan');

        } catch (Exception $e) {
            Log::error('Error Cancel Ajukan Shift: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}