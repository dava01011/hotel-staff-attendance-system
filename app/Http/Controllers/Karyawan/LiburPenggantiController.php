<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\PengajuanLiburPengganti;
use App\Models\LiburPengganti;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class LiburPenggantiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $saldo = LiburPengganti::where('karyawan_id', $karyawan->id)->first();

        $aktif = PengajuanLiburPengganti::with('approvals.approver')
            ->where('karyawan_id', $karyawan->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $selesai = PengajuanLiburPengganti::with('approvals.approver')
            ->where('karyawan_id', $karyawan->id)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->latest()
            ->get();

        return view('karyawan.libur-pengganti.index', compact('saldo', 'aktif', 'selesai'));
    }

    public function create()
    {
        $karyawan = Auth::user()->karyawan;
        $saldo = LiburPengganti::where('karyawan_id', $karyawan->id)->first();
        return view('karyawan.libur-pengganti.create', compact('saldo'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'tanggal'         => 'required|date|after_or_equal:today',
                'alasan'          => 'required|string|min:10|max:500',
                'file_pendukung'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            $user = Auth::user();
            $karyawan = $user->karyawan;

            // Cek saldo
            $saldo = LiburPengganti::where('karyawan_id', $karyawan->id)->first();
            if (!$saldo || $saldo->saldo <= 0) {
                return back()->with('error', 'Saldo libur pengganti Anda tidak mencukupi.');
            }

            // Cek bentrok dengan pengajuan lain yang pending/disetujui
            $bentrok = PengajuanLiburPengganti::where('karyawan_id', $karyawan->id)
                ->where('status', '!=', 'ditolak')
                ->whereDate('tanggal', $request->tanggal)
                ->exists();

            if ($bentrok) {
                return back()->with('error', 'Anda sudah memiliki pengajuan pada tanggal tersebut.');
            }

            $filePath = null;
            if ($request->hasFile('file_pendukung')) {
                $file = $request->file('file_pendukung');
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

            $pengajuan->createApprovalSteps($user->role);

            activity_log(
                'libur_pengganti',
                'create',
                "Mengajukan libur pengganti tanggal {$request->tanggal}"
            );

            Notifikasi::create([
                'user_id'     => $user->id,
                'judul'       => 'Pengajuan Libur Pengganti Berhasil',
                'pesan'       => "Pengajuan libur pengganti tanggal {$request->tanggal} berhasil dikirim.",
                'type'        => 'libur_pengganti',
                'target_role' => 'karyawan',
            ]);

            DB::commit();

            return redirect()->route('karyawan.libur-pengganti.index')
                ->with('success', 'Pengajuan libur pengganti berhasil dikirim.');

        } catch (Exception $e) {
            DB::rollBack();
            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            Log::error('Error Store Libur Pengganti: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel($id)
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
                    'catatan_admin' => 'Dibatalkan oleh pemohon',
                ]);
                $pengajuan->approvals()->update(['status' => 'ditolak']);
            });

            return back()->with('success', 'Pengajuan berhasil dibatalkan.');
        } catch (Exception $e) {
            Log::error('Error Cancel Libur Pengganti: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}