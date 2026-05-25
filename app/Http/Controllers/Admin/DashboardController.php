<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Helpers\RoleHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Cek apakah user bisa melihat semua data (tanpa filter departemen)
     */
    public function canViewAll(): bool
    {
        return RoleHelper::isSuperAdmin();
    }

    /**
     * Ambil ID departemen user yang sedang login
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
     * Terapkan filter departemen untuk query
     */
    private function applyDepartmentFilter($query, string $type = 'karyawan')
    {
        // Super Admin bisa lihat semua
        if ($this->canViewAll()) {
            return $query;
        }

        $deptId = $this->getUserDepartmentId();
        if (!$deptId) {
            return $query;
        }

        switch ($type) {
            case 'karyawan':
                return $query->where('departemen_id', $deptId);
            case 'absensi':
            case 'cuti':
                return $query->whereHas('karyawan', function ($q) use ($deptId) {
                    $q->where('departemen_id', $deptId);
                });
            default:
                return $query;
        }
    }

    public function index()
    {
        try {
            $today    = Carbon::today();
            $bulanIni = Carbon::now()->month;
            $tahunIni = Carbon::now()->year;

            // Total Karyawan (difilter)
            $totalKaryawan = $this->applyDepartmentFilter(
                Karyawan::where('status', 'aktif'),
                'karyawan'
            )->count();

            // Absensi hari ini (difilter)
            $absensiToday = $this->applyDepartmentFilter(
                Absensi::whereDate('tanggal', $today),
                'absensi'
            );

            $hadirHariIni     = (clone $absensiToday)->whereIn('status', ['hadir', 'terlambat'])->count();
            $terlambatHariIni = (clone $absensiToday)->where('status', 'terlambat')->count();
            $cutiHariIni      = (clone $absensiToday)->where('status', 'cuti')->count();
            $alpaHariIni      = max(0, $totalKaryawan - $hadirHariIni - $cutiHariIni);
            $persentaseHadir  = $totalKaryawan > 0
                ? round(($hadirHariIni / $totalKaryawan) * 100)
                : 0;

            // Total absensi bulan ini (difilter)
            $totalAbsensiBulanIni = $this->applyDepartmentFilter(
                Absensi::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni),
                'absensi'
            )->count();

            // Trend 7 hari (difilter per hari)
            $trend7Hari = collect(range(6, 0))->map(function ($i) use ($today) {
                $date = Carbon::today()->subDays($i);
                $query = $this->applyDepartmentFilter(
                    Absensi::whereDate('tanggal', $date),
                    'absensi'
                );

                return [
                    'label'     => $date->translatedFormat('D'),
                    'tanggal'   => $date->format('d/m'),
                    'hadir'     => (clone $query)->whereIn('status', ['hadir', 'terlambat'])->count(),
                    'terlambat' => (clone $query)->where('status', 'terlambat')->count(),
                ];
            });

            // Cuti pending (difilter)
            $cutiPending = $this->applyDepartmentFilter(
                Cuti::where('status', 'pending'),
                'cuti'
            )->count();

            // Recent absensi (difilter)
            $recentAbsensi = $this->applyDepartmentFilter(
                Absensi::with(['karyawan.user', 'karyawan.jabatan'])
                    ->whereDate('tanggal', $today)
                    ->orderBy('created_at', 'desc')
                    ->limit(8),
                'absensi'
            )->get();

            // Hak akses approval (gunakan RoleHelper)
            $canApprove = RoleHelper::canAccessApproval();

            return view('admin.dashboard', compact(
                'today',
                'totalKaryawan',
                'hadirHariIni',
                'terlambatHariIni',
                'cutiHariIni',
                'alpaHariIni',
                'persentaseHadir',
                'totalAbsensiBulanIni',
                'trend7Hari',
                'cutiPending',
                'recentAbsensi',
                'canApprove'
            ));
        } catch (\Exception $e) {
            // Untuk debugging, bisa di-log atau tampilkan pesan
            // return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            abort(500, 'Dashboard Error: ' . $e->getMessage());
        }
    }
}