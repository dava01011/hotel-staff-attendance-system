<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Karyawan;
use App\Models\AjukanShift;
use App\Models\KaryawanShiftPattern;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AjukanShiftController extends Controller
{
    /**
     * Tampilkan halaman pengajuan shift
     */
    public function index()
    {
        // Cek apakah user adalah admin dengan departemen
        $user = Auth::user();

        // Ambil departemen dari admin (bisa berdasarkan relasi atau hardcoded)
        // Jika belum ada relasi, gunakan departemen dari request atau session
        $departemen = Departemen::first(); // Sesuaikan logika ini

        if (!$departemen) {
            return redirect()->route('admin.dashboard')->with('error', 'Departemen tidak ditemukan.');
        }

        // Ambil shift aktif departemen (referensi dari karyawan pertama)
        $firstKaryawan = Karyawan::where('departemen_id', $departemen->id)->first();
        $jadwalShiftAktif = null;
        if ($firstKaryawan) {
            $jadwalShiftAktif = KaryawanShiftPattern::where('karyawan_id', $firstKaryawan->id)
                ->where('is_default', true)
                ->where('hari', 'senin')
                ->with('shift')
                ->first();
        }

        // Riwayat pengajuan milik DEPARTEMEN INI
        $riwayatPengajuan = AjukanShift::where('departemen_id', $departemen->id)
            ->with(['shiftLama', 'shiftBaru', 'pemohon'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Statistik DEPARTEMEN INI
        $totalPengajuan = AjukanShift::where('departemen_id', $departemen->id)->count();
        $pengajuanPending = AjukanShift::where('departemen_id', $departemen->id)->where('status', 'pending')->count();
        $pengajuanDisetujui = AjukanShift::where('departemen_id', $departemen->id)->where('status', 'disetujui')->count();
        $pengajuanDitolak = AjukanShift::where('departemen_id', $departemen->id)->where('status', 'ditolak')->count();

        // Shift master
        $allShifts = Shift::all();

        // Cek pending
        $pendingPengajuan = AjukanShift::where('departemen_id', $departemen->id)
            ->where('status', 'pending')
            ->first();

        return view('admin.ajukan-shift.index', compact(
            'departemen',
            'jadwalShiftAktif',
            'allShifts',
            'riwayatPengajuan',
            'pendingPengajuan',
            'totalPengajuan',
            'pengajuanPending',
            'pengajuanDisetujui',
            'pengajuanDitolak'
        ));
    }

    /**
     * Proses pengajuan shift
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'jenis' => 'required|in:sementara,permanen',
                'shift_baru_id' => 'required|exists:shift,id',
                'tanggal_mulai' => 'required|date|after_or_equal:today',
                'tanggal_selesai' => 'required_if:jenis,sementara|nullable|date|after_or_equal:tanggal_mulai',
                'alasan' => 'nullable|string|max:500'
            ]);

            $departemen = Departemen::first(); // Sesuaikan logika ini

            if (!$departemen) {
                return back()->with('error', 'Departemen tidak ditemukan.');
            }

            // Cek apakah ada pengajuan yang masih pending
            $pendingExists = AjukanShift::where('departemen_id', $departemen->id)
                ->where('status', 'pending')
                ->exists();

            if ($pendingExists) {
                return back()->with('error', 'Masih ada pengajuan shift yang belum diproses. Tunggu hingga disetujui atau ditolak terlebih dahulu.');
            }

            // Ambil shift aktif saat ini (referensi dari karyawan pertama)
            $firstKaryawan = Karyawan::where('departemen_id', $departemen->id)->first();
            $jadwalShiftAktif = null;
            if ($firstKaryawan) {
                $jadwalShiftAktif = KaryawanShiftPattern::where('karyawan_id', $firstKaryawan->id)
                    ->where('is_default', true)
                    ->where('hari', 'senin')
                    ->first();
            }

            if (!$jadwalShiftAktif) {
                return back()->with('error', 'Shift aktif departemen tidak dapat ditentukan.');
            }

            $shiftLamaId = $jadwalShiftAktif->shift_id;
            $shiftBaruId = $request->shift_baru_id;

            // Validasi: shift baru tidak boleh sama dengan shift lama
            if ($shiftLamaId == $shiftBaruId) {
                return back()->with('error', 'Shift pengganti tidak boleh sama dengan shift saat ini!');
            }

            // Untuk shift permanen, tanggal_selesai = null
            $tanggalSelesai = $request->jenis == 'permanen' ? null : $request->tanggal_selesai;

            // Cek overlap untuk shift sementara yang sudah disetujui
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

            // Buat pengajuan
            AjukanShift::create([
                'departemen_id' => $departemen->id,
                'shift_lama_id' => $shiftLamaId,
                'shift_baru_id' => $shiftBaruId,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $tanggalSelesai,
                'jenis' => $request->jenis,
                'requested_by' => Auth::id(),
                'alasan' => $request->alasan,
                'status' => 'pending'
            ]);

            $jenisText = $request->jenis == 'sementara' ? 'sementara' : 'permanen';

            return redirect()->route('admin-dept.ajukan-shift.index')
                ->with('success', "Pengajuan shift {$jenisText} berhasil diajukan! Menunggu persetujuan admin.");

        } catch (Exception $e) {
            Log::error('Error Ajukan Shift: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan pengajuan (hanya yang masih pending)
     */
    public function cancel($id)
    {
        try {
            $departemen = Departemen::first();

            $ajukan = AjukanShift::where('id', $id)
                ->where('departemen_id', $departemen->id)
                ->where('status', 'pending')
                ->firstOrFail();

            $ajukan->delete();

            return redirect()->route('admin-dept.ajukan-shift.index')
                ->with('success', 'Pengajuan shift berhasil dibatalkan.');

        } catch (Exception $e) {
            Log::error('Error Cancel Ajukan Shift: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Lihat detail pengajuan
     */
    public function show($id)
    {
        try {
            $departemen = Departemen::first();

            $ajukan = AjukanShift::where('id', $id)
                ->where('departemen_id', $departemen->id)
                ->with(['shiftLama', 'shiftBaru', 'pemohon', 'approver', 'departemen'])
                ->firstOrFail();

            return view('admin.ajukan-shift.detail', compact('ajukan'));

        } catch (Exception $e) {
            Log::error('Error Show Ajukan Shift: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
