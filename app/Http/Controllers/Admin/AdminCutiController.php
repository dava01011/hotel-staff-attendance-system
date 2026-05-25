<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Cuti;
use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\CarbonPeriod;
use App\Models\JatahCuti;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminCutiController extends Controller
{
    /**
     * Check if user can approve/reject cuti
     */
    private function canApproveCuti()
    {
        return in_array(Auth::user()->role, ['super_admin', 'admin', 'gm']);
    }

    /**
     * Display a listing of the resource with role-based filtering.
     */
    /**
     * Check if user can view all data (without department restriction)
     * Super Admin, HRD, Manager, GM
     */
    private function canViewAll(): bool
    {
        return in_array(Auth::user()->role, ['super_admin', 'gm']);
    }

    /**
     * Get current user's department ID (for admin only)
     */
    private function getUserDepartmentId()
    {
        $user = Auth::user();
        if ($user && $user->karyawan) {
            return $user->karyawan->departemen_id;
        }
        return null;
    }

    /**
     * Display a listing of the resource (riwayat cuti).
     */
    public function index()
    {
        $user = Auth::user();
        $query = Cuti::with(['karyawan.user', 'karyawan.departemen', 'jenisCuti'])
            ->latest();

        // Role-based filtering: hanya admin yang dibatasi departemennya
        if (!$this->canViewAll()) {
            $deptId = $this->getUserDepartmentId();
            if ($deptId) {
                $query->whereHas('karyawan', function ($q) use ($deptId) {
                    $q->where('departemen_id', $deptId);
                });
            }
        }

        $cuti = $query->get();

        return view('admin.cuti.index', [
            'cuti'     => $cuti,
            'userRole' => $user->role,
        ]);
    }

    /**
     * Approve cuti request
     */
    public function approve(Request $request, $id)
    {
        // Authorization check
        if (!$this->canApproveCuti()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menyetujui cuti');
        }

        $cuti = Cuti::findOrFail($id);
        $user = Auth::user();

        // Department check untuk admin
        if ($user->role !== 'super_admin') {
            $departemenId = $user->karyawan->departemen_id ?? null;
            if ($departemenId !== $cuti->karyawan->departemen_id) {
                return back()->with('error', 'Anda hanya dapat menyetujui cuti dari departemen Anda');
            }
        }

        $karyawan = $cuti->karyawan;
        $jumlahHari = Carbon::parse($cuti->tanggal_mulai)->diffInDays(Carbon::parse($cuti->tanggal_selesai)) + 1;

        DB::transaction(function () use ($cuti, $karyawan, $jumlahHari) {
            // Lock untuk update jatah cuti
            $jatah = JatahCuti::where('karyawan_id', $karyawan->id)
                ->where('tahun', now()->year)
                ->lockForUpdate()
                ->first();

            // Jika jatah tidak ada, create dengan default 12 hari
            if (!$jatah) {
                $jatah = JatahCuti::create([
                    'karyawan_id' => $karyawan->id,
                    'tahun' => now()->year,
                    'jatah_awal' => 0,
                    'jatah' => 0,
                ]);
            }

            // Check if sufficient quota
            if ($jatah->jatah < $jumlahHari) {
                throw new \Exception('Saldo cuti karyawan tidak mencukupi. Tersedia: ' . $jatah->jatah . ' hari, diminta: ' . $jumlahHari . ' hari');
            }

            // Decrement quota
            $jatah->decrement('jatah', $jumlahHari);

            // Update cuti status
            $cuti->update([
                'status' => 'disetujui',
                'disetujui_oleh' => auth()->id(),
                'tanggal_disetujui' => now(),
            ]);

            // Create success notification
            Notifikasi::create([
                'user_id' => $cuti->karyawan->user->id,
                'judul' => 'Cuti Disetujui',
                'pesan' => 'Pengajuan cuti Anda telah disetujui. Tersisa ' . $jatah->jatah . ' hari cuti.',
                'type' => 'cuti',
                'target_role' => 'karyawan'
            ]);

            // Alert if quota is low
            if ($jatah->jatah <= 1) {
                Notifikasi::create([
                    'user_id' => $cuti->karyawan->user->id,
                    'judul' => 'Sisa Cuti Hampir Habis',
                    'pesan' => 'Sisa jatah cuti Anda tinggal ' . $jatah->jatah . ' hari.',
                    'type' => 'cuti',
                    'target_role' => 'karyawan'
                ]);
            }

            // Insert absensi CUTI per hari
            $tanggal = Carbon::parse($cuti->tanggal_mulai);

            while ($tanggal->lte($cuti->tanggal_selesai)) {
                Absensi::updateOrCreate([
                    'karyawan_id' => $karyawan->id,
                    'tanggal' => $tanggal->toDateString(),
                ], [
                    'status' => 'cuti',
                ]);

                $tanggal->addDay();
            }
        });

        return back()->with('success', 'Pengajuan cuti berhasil disetujui');
    }

    /**
     * Reject cuti request
     */
    public function reject(Request $request, $id)
    {
        // Authorization check
        if (!$this->canApproveCuti()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menolak cuti');
        }

        $request->validate([
            'catatan_admin' => 'nullable|string|max:500'
        ]);

        $cuti = Cuti::findOrFail($id);
        $user = Auth::user();

        // Department check untuk admin
        if ($user->role !== 'super_admin') {
            $departemenId = $user->karyawan->departemen_id ?? null;
            if ($departemenId !== $cuti->karyawan->departemen_id) {
                return back()->with('error', 'Anda hanya dapat menolak cuti dari departemen Anda');
            }
        }

        // Update cuti status
        $cuti->update([
            'status' => 'ditolak',
            'catatan_admin' => $request->catatan_admin,
            'ditolak_oleh' => auth()->id(),
            'tanggal_ditolak' => now(),
        ]);

        // Create rejection notification
        $pesan = "Pengajuan cuti Anda telah ditolak.";

        if (!empty($request->catatan_admin)) {
            $pesan .= "\n\nCatatan Admin:\n" . $request->catatan_admin;
        } else {
            $pesan .= "\n\nSilakan hubungi HR untuk informasi lebih lanjut.";
        }

        Notifikasi::create([
            'user_id' => $cuti->karyawan->user->id,
            'judul' => 'Cuti Ditolak',
            'pesan' => $pesan,
            'type' => 'cuti',
            'target_role' => 'karyawan'
        ]);

        return back()->with('success', 'Pengajuan cuti berhasil ditolak');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}