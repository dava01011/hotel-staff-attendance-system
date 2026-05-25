<?php

namespace App\Http\Controllers\Karyawan;

use Exception;
use Carbon\Carbon;
use App\Models\Cuti;
use App\Models\JatahCuti;
use App\Models\JenisCuti;
use App\Models\Notifikasi;
use App\Models\CutiApproval;
use App\Models\AjukanShift;
use App\Models\KaryawanShiftPattern;
use App\Models\PengajuanLiburPengganti;
use App\Models\LiburPengganti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\CutiDiajukanMail;
use Illuminate\Support\Facades\Mail;

class PengajuanController extends Controller
{
    /**
     * Display unified index page with both cuti and shift data
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        // ===== DATA LIBUR PENGGANTI =====
        $saldoLibur = LiburPengganti::where('karyawan_id', $karyawan->id)->first();

        $liburAktif = PengajuanLiburPengganti::with('approvals')
            ->where('karyawan_id', $karyawan->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $liburSelesai = PengajuanLiburPengganti::with('approvals')
            ->where('karyawan_id', $karyawan->id)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->latest()
            ->take(3)
            ->get();

        // ===== DATA CUTI =====
        $jatahCuti = JatahCuti::where('karyawan_id', $karyawan->id)
            ->where('tahun', now()->year)
            ->first();

        $cutiAktif = Cuti::with(['jenisCuti', 'approvals'])
            ->where('karyawan_id', $karyawan->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $cutiSelesai = Cuti::with(['jenisCuti', 'approvals'])
            ->where('karyawan_id', $karyawan->id)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->latest()
            ->take(3)
            ->get();

        // ===== DATA SHIFT (hanya untuk role admin) =====
        $jadwalShiftAktif = null;
        $shiftAktif = collect();
        $shiftSelesai = collect();

        $adminRoles = ['admin', 'super_admin'];
        $isAdmin = in_array($user->role, $adminRoles);

        if ($isAdmin && $karyawan->departemen_id) {
            $departemen = $karyawan->departemen;

            $jadwalShiftAktif = KaryawanShiftPattern::where('karyawan_id', $karyawan->id)
                ->where('is_default', true)
                ->where('hari', 'senin')
                ->with('shift')
                ->first();

            // $shiftAktif = AjukanShift::with(['shiftLama', 'shiftBaru', 'pemohon', 'approver'])
            //     ->where('departemen_id', $departemen->id)
            //     ->where('requested_by', $user->id)
            //     ->where('status', 'pending')
            //     ->latest()
            //     ->get();

            // $shiftSelesai = AjukanShift::with(['shiftLama', 'shiftBaru', 'pemohon', 'approver'])
            //     ->where('departemen_id', $departemen->id)
            //     ->where('requested_by', $user->id)
            //     ->whereIn('status', ['disetujui', 'ditolak'])
            //     ->latest()
            //     ->take(3)
            //     ->get();
            
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
        }

        return view('karyawan.pengajuan.index', compact(
            'jatahCuti',
            'cutiAktif',
            'cutiSelesai',
            'jadwalShiftAktif',
            'shiftAktif',
            'shiftSelesai',
            'saldoLibur',
            'liburAktif',
            'liburSelesai'
        ));
    }

    /**
     * Show detail pengajuan libur pengganti
     */
    public function showLiburPengganti($id)
    {
        $karyawan = Auth::user()->karyawan;
        $pengajuan = PengajuanLiburPengganti::with(['approvals.approver'])
            ->where('karyawan_id', $karyawan->id)
            ->findOrFail($id);

        return view('karyawan.pengajuan.libur-detail', compact('pengajuan'));
    }

    /**
     * Show riwayat libur pengganti dengan filter
     */
    public function riwayatLiburPengganti(Request $request)
    {
        $karyawan = Auth::user()->karyawan;

        $query = PengajuanLiburPengganti::with(['approvals.approver'])
            ->where('karyawan_id', $karyawan->id)
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && in_array($request->status, ['pending', 'disetujui', 'ditolak'])) {
            $query->where('status', $request->status);
        }

        $liburPengganti = $query->paginate(15)->appends($request->query());
        $currentStatus = $request->status ?? 'all';

        return view('karyawan.pengajuan.libur-riwayat', compact('liburPengganti', 'currentStatus'));
    }

    /**
     * Show create form for cuti
     */
    public function create()
    {
        $jenisCuti = JenisCuti::all();
        return view('karyawan.pengajuan.create', compact('jenisCuti'));
    }

    /**
     * Show create form for libur pengganti
     */
    public function createLiburPengganti()
    {
        $karyawan = Auth::user()->karyawan;
        $saldo = LiburPengganti::where('karyawan_id', $karyawan->id)->first();

        return view('karyawan.pengajuan.libur-create', compact('saldo'));
    }

    /**
     * Store new pengajuan libur pengganti
     */
    public function storeLiburPengganti(Request $request)
    {
        try {
            $request->validate([
                'tanggal'         => 'required|date|after_or_equal:today',
                'alasan'          => 'required|string|min:10|max:500',
                'file_pendukung'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);

            $user     = Auth::user();
            $karyawan = $user->karyawan;

            // Cek saldo
            $saldo = LiburPengganti::where('karyawan_id', $karyawan->id)->first();
            if (!$saldo || $saldo->saldo <= 0) {
                return back()->with('error', 'Saldo libur pengganti Anda tidak mencukupi.');
            }

            // Cek bentrok
            $bentrok = PengajuanLiburPengganti::where('karyawan_id', $karyawan->id)
                ->where('status', '!=', 'ditolak')
                ->whereDate('tanggal', $request->tanggal)
                ->exists();

            if ($bentrok) {
                return back()->with('error', 'Anda sudah memiliki pengajuan pada tanggal tersebut.');
            }

            $filePath = null;
            if ($request->hasFile('file_pendukung')) {
                $file     = $request->file('file_pendukung');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('libur-pengganti', $fileName, 'public');
            }

            DB::beginTransaction();

            $pengajuan = PengajuanLiburPengganti::create([
                'karyawan_id'    => $karyawan->id,
                'tanggal'        => $request->tanggal,
                'alasan'         => $request->alasan,
                'file_pendukung' => $filePath,
                'status'         => 'pending',
            ]);

            // Buat approval steps menggunakan method model
            $pengajuan->createApprovalSteps($user->role);

            activity_log('libur_pengganti', 'create', "Mengajukan libur pengganti tanggal {$request->tanggal}");

            Notifikasi::create([
                'user_id'     => $user->id,
                'judul'       => 'Pengajuan Libur Pengganti Berhasil',
                'pesan'       => "Pengajuan libur pengganti tanggal {$request->tanggal} berhasil dikirim.",
                'type'        => 'libur_pengganti',
                'target_role' => 'karyawan'
            ]);

            // Notifikasi ke approver pertama
            $firstStep = $pengajuan->current_step;
            if ($firstStep) {
                Notifikasi::create([
                    'user_id'     => $user->id,
                    'judul'       => 'Pengajuan Libur Pengganti Baru',
                    'pesan'       => "{$karyawan->user->nama} mengajukan libur pengganti tanggal {$request->tanggal}.",
                    'type'        => 'libur_pengganti',
                    'target_role' => $firstStep
                ]);
            }

            DB::commit();

            return redirect()->route('karyawan.pengajuan.index')
                ->with('success', 'Pengajuan libur pengganti berhasil dikirim');

        } catch (Exception $e) {
            DB::rollBack();
            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            Log::error('Error Store Libur Pengganti: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel pengajuan libur pengganti
     */
    public function cancelLiburPengganti($id)
    {
        try {
            $pengajuan = PengajuanLiburPengganti::findOrFail($id);

            if ($pengajuan->karyawan->user_id !== Auth::id()) {
                return back()->with('error', 'Anda tidak memiliki akses.');
            }

            if ($pengajuan->status !== 'pending') {
                return back()->with('error', 'Hanya pengajuan pending yang dapat dibatalkan.');
            }

            DB::transaction(function () use ($pengajuan) {
                $pengajuan->update([
                    'status'        => 'ditolak',
                    'catatan_admin' => 'Dibatalkan oleh pemohon'
                ]);
                $pengajuan->approvals()->update(['status' => 'ditolak']);
            });

            return back()->with('success', 'Pengajuan berhasil dibatalkan.');
        } catch (Exception $e) {
            Log::error('Error Cancel Libur Pengganti: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store new cuti
     */
    public function storeCuti(Request $request)
    {
        try {
            $request->validate([
                'jenis_id'        => 'required|exists:jenis_cuti,id',
                'tanggal_mulai'   => 'required|date|after_or_equal:today',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'alasan'          => 'required|string|min:10|max:500',
                'file_pendukung'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);

            $user     = Auth::user();
            $karyawan = $user->karyawan;

            $jenisCuti    = JenisCuti::findOrFail($request->jenis_id);
            $tanggalMulai = Carbon::parse($request->tanggal_mulai);
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
            $jumlahHari   = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

            if ($jenisCuti->max_hari && $jumlahHari > $jenisCuti->max_hari) {
                return back()->with('error', "Maksimal {$jenisCuti->nama} adalah {$jenisCuti->max_hari} hari");
            }

            $jatah = JatahCuti::where('karyawan_id', $karyawan->id)
                ->where('tahun', now()->year)
                ->first();

            if (!$jatah || $jatah->jatah < $jumlahHari) {
                return back()->with('error', 'Sisa jatah cuti Anda tidak mencukupi');
            }

            $bentrok = Cuti::where('karyawan_id', $karyawan->id)
                ->where('status', '!=', 'ditolak')
                ->where(function ($query) use ($request) {
                    $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai])
                        ->orWhereBetween('tanggal_selesai', [$request->tanggal_mulai, $request->tanggal_selesai])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('tanggal_mulai', '<=', $request->tanggal_mulai)
                              ->where('tanggal_selesai', '>=', $request->tanggal_selesai);
                        });
                })->exists();

            if ($bentrok) {
                return back()->with('error', 'Tanggal cuti bentrok dengan pengajuan cuti lain');
            }

            $filePath = null;
            if ($request->hasFile('file_pendukung')) {
                $file     = $request->file('file_pendukung');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('cuti-files', $fileName, 'public');
            }

            DB::beginTransaction();

            $approvalSteps = array_unique(Cuti::getApprovalSteps($user->role));
            $firstStep     = $approvalSteps[0] ?? null;

            $cuti = Cuti::create([
                'karyawan_id'     => $karyawan->id,
                'jenis_id'        => $request->jenis_id,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'alasan'          => $request->alasan,
                'file_pendukung'  => $filePath,
                'status'          => empty($approvalSteps) ? 'disetujui' : 'pending',
                'current_step'    => $firstStep,
                'is_bentrok'      => false
            ]);

            if (!empty($approvalSteps)) {
                foreach ($approvalSteps as $step) {
                    CutiApproval::create([
                        'cuti_id' => $cuti->id,
                        'step'    => $step,
                        'status'  => 'pending'
                    ]);
                }
            } else {
                $jatah->decrement('jatah', $jumlahHari);
            }

            activity_log(
                'cuti',
                'create',
                "Mengajukan cuti {$jenisCuti->nama} tanggal {$request->tanggal_mulai} s/d {$request->tanggal_selesai}"
            );

            Notifikasi::create([
                'user_id'     => $user->id,
                'judul'       => 'Pengajuan Cuti Berhasil',
                'pesan'       => "Pengajuan {$jenisCuti->nama} Anda berhasil dikirim dan menunggu persetujuan.",
                'type'        => 'cuti',
                'target_role' => 'karyawan'
            ]);

            if ($firstStep) {
                Notifikasi::create([
                    'user_id'     => $user->id,
                    'judul'       => 'Pengajuan Cuti Baru',
                    'pesan'       => "{$karyawan->user->nama} mengajukan {$jenisCuti->nama} ({$jumlahHari} hari) yang memerlukan persetujuan " . ucfirst($firstStep) . ".",
                    'type'        => 'cuti',
                    'target_role' => $firstStep
                ]);
            }

            DB::commit();

            // Kirim email ke approver pertama
            if (!empty($approvalSteps)) {
                $firstStep = $approvalSteps[0];
                $firstApproval = $cuti->approvals()->where('step', $firstStep)->first();

                if ($firstApproval) {
                    $approverUser = $firstApproval->getApproverUser();
                    $email = $approverUser->email ?? null;

                    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($email)->queue(new CutiDiajukanMail($cuti));
                    }
                }
            }

            return redirect()->route('karyawan.pengajuan.index')
                ->with('success', 'Pengajuan cuti berhasil dikirim');

        } catch (Exception $e) {
            DB::rollBack();

            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            Log::error('Error Store Cuti: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show riwayat page
     */
    public function riwayat()
    {
        $karyawan = Auth::user()->karyawan;

        $cuti = Cuti::with(['jenisCuti', 'approvals'])
            ->where('karyawan_id', $karyawan->id)
            ->latest()
            ->paginate(20);

        return view('karyawan.pengajuan.riwayat', compact('cuti'));
    }

    /**
     * Show detail cuti
     */
    public function showCuti($id)
    {
        $cuti = Cuti::with([
                'karyawan.user',
                'jenisCuti',
                'approvals'
            ])
            ->findOrFail($id);

        if ($cuti->karyawan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('karyawan.pengajuan.detail', compact('cuti'));
    }

    /**
     * Cancel cuti
     */
    public function cancelCuti($id)
    {
        try {
            $cuti = Cuti::findOrFail($id);

            if ($cuti->karyawan->user_id !== Auth::id()) {
                return back()->with('error', 'Anda tidak memiliki akses');
            }

            if ($cuti->status !== 'pending') {
                return back()->with('alert', [
                    'type'    => 'error',
                    'title'   => 'Batalkan Pengajuan',
                    'message' => 'Hanya cuti yang sedang pending yang dapat dibatalkan'
                ]);
            }

            DB::transaction(function () use ($cuti) {
                $cuti->update([
                    'status'        => 'ditolak',
                    'catatan_admin' => 'Dibatalkan oleh pemohon'
                ]);

                $cuti->approvals()->update(['status' => 'ditolak']);

                activity_log(
                    'cuti',
                    'cancel',
                    "Membatalkan pengajuan cuti tanggal {$cuti->tanggal_mulai->format('d/m/Y')}"
                );
            });

            return back()->with('alert', [
                'type'    => 'success',
                'title'   => 'Batalkan Pengajuan',
                'message' => 'Pengajuan cuti berhasil dibatalkan'
            ]);

        } catch (Exception $e) {
            Log::error('Error Cancel Cuti: ' . $e->getMessage());

            return back()->with('alert', [
                'type'    => 'error',
                'title'   => 'Gagal!',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}