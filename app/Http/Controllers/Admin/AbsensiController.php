<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AbsensiExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Services\AbsensiDetectionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    /**
     * Cek apakah user bisa CRUD (Super Admin & Admin)
     */
    private function canCRUD(): bool
    {
        return \App\Helpers\RoleHelper::canCrudAbsensi();
    }

    /**
     * Get current user's department ID
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
     * Apply department filter untuk admin
     */
    private function applyDepartmentFilter($query)
    {
        $user = Auth::user();
        $role = $user->role;
        $deptId = $this->getUserDepartmentId();

        if ($role === 'admin' && $deptId) {
            $query->whereHas('karyawan', function ($q) use ($deptId) {
                $q->where('departemen_id', $deptId);
            });
        }

        return $query;
    }

    /**
     * Build query dengan filter
     */
    private function buildQuery(Request $request)
    {
        $query = Absensi::with([
            'karyawan.user',
            'karyawan.departemen',
            'karyawan.jabatan',
        ]);

        $query = $this->applyDepartmentFilter($query);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('tanggal', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan.user', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('tanggal', 'desc')->orderBy('jam_masuk', 'desc');
    }

    /**
     * Get active filters
     */
    private function getFilters(Request $request): array
    {
        return [
            'status'    => $request->get('status', 'all'),
            'date_from' => $request->get('date_from'),
            'date_to'   => $request->get('date_to'),
            'search'    => $request->get('search'),
        ];
    }

    /**
     * ✅ GET LOGO AS BASE64 - OPTIMIZED
     * Cache 24 jam untuk performa
     * Supports large size tanpa lag
     */
    private function getLogoBase64(): ?string
    {
        return Cache::remember('logo_base64', 24 * 60, function () {
            $logoPath = public_path('img/Logo.png');
            
            if (!file_exists($logoPath)) {
                return null;
            }

            try {
                $imageData = @file_get_contents($logoPath);
                if ($imageData === false) return null;

                $base64 = base64_encode($imageData);
                
                $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                $mimeType = match(strtolower($extension)) {
                    'png'  => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'gif'  => 'image/gif',
                    default => 'image/png'
                };

                return 'data:' . $mimeType . ';base64,' . $base64;

            } catch (\Exception $e) {
                Log::error('Error processing logo: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Index
     */
    public function index(Request $request)
    {
        $absensi = $this->buildQuery($request)->get();
        $filters = $this->getFilters($request);
        $canCRUD = $this->canCRUD();
        $userRole = Auth::user()->role;

        // Data karyawan untuk dropdown tambah absen manual
        $karyawanList = [];
        if ($canCRUD) {
            $kQuery = Karyawan::with('user')->where('status', 'aktif');
            $deptId = $this->getUserDepartmentId();
            if ($userRole === 'admin' && $deptId) {
                $kQuery->where('departemen_id', $deptId);
            }
            $karyawanList = $kQuery->get();
        }

        return view('admin.absensi.index', compact('absensi', 'filters', 'canCRUD', 'userRole', 'karyawanList'));
    }

    /**
     * Store manual attendance
     * POST /admin/absensi
     */
    public function store(Request $request)
    {
        if (!$this->canCRUD()) {
            abort(403);
        }

        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal'     => 'required|date',
            'jam_masuk'   => 'nullable',
            'jam_pulang'  => 'nullable',
            'status'      => 'required|in:hadir,terlambat,izin,sakit,cuti,alpa',
        ]);

        // Proteksi departemen untuk role admin
        if (Auth::user()->role === 'admin') {
            $karyawan = Karyawan::findOrFail($request->karyawan_id);
            if ($karyawan->departemen_id != $this->getUserDepartmentId()) {
                return back()->with('error', 'Anda tidak memiliki akses ke karyawan departemen lain.');
            }
        }

        Absensi::updateOrCreate([
            'karyawan_id' => $request->karyawan_id,
            'tanggal'     => $request->tanggal,
        ], [
            'jam_masuk'  => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
            'status'     => $request->status,
            'verification_method' => 'manual',
        ]);

        return back()->with('success', 'Absensi manual berhasil disimpan.');
    }

    /**
     * Update attendance
     * PUT /admin/absensi/{id}
     */
    public function update(Request $request, $id)
    {
        if (!$this->canCRUD()) {
            abort(403);
        }

        $absensi = Absensi::with('karyawan')->findOrFail($id);

        // Proteksi departemen
        if (Auth::user()->role === 'admin' && $absensi->karyawan->departemen_id != $this->getUserDepartmentId()) {
            return back()->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        $request->validate([
            'jam_masuk'  => 'nullable',
            'jam_pulang' => 'nullable',
            'status'     => 'required|in:hadir,terlambat,izin,sakit,cuti,alpa',
        ]);

        $absensi->update($request->only(['jam_masuk', 'jam_pulang', 'status']));

        return back()->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Delete attendance
     * DELETE /admin/absensi/{id}
     */
    public function destroy($id)
    {
        if (!$this->canCRUD()) {
            abort(403);
        }

        $absensi = Absensi::with('karyawan')->findOrFail($id);

        // Proteksi departemen
        if (Auth::user()->role === 'admin' && $absensi->karyawan->departemen_id != $this->getUserDepartmentId()) {
            return back()->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        $absensi->delete();

        return back()->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        $absensi = $this->buildQuery($request)->get();
        $filters = $this->getFilters($request);

        $filename = 'laporan-absensi-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new AbsensiExport($absensi, $filters), $filename);
    }

    /**
     * Build PDF query - default ke bulan ini jika tidak ada filter tanggal
     */
    private function buildPdfQuery(Request $request)
    {
        // ✅ Jika tidak ada filter tanggal, default ke bulan ini
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $request->merge([
                'date_from' => now()->startOfMonth()->format('Y-m-d'),
                'date_to'   => now()->endOfMonth()->format('Y-m-d'),
            ]);
        }

        return $this->buildQuery($request)->get();
    }

    /**
     * Preview PDF (inline)
     * GET /admin/absensi/preview-pdf
     */
    public function previewPdf(Request $request)
    {
        try {
            // ✅ Naikkan memory limit untuk DomPDF
            ini_set('memory_limit', '1024M');
            set_time_limit(180);

            $absensi = $this->buildPdfQuery($request);
            $filters = $this->getFilters($request);
            
            $logoBase64 = $this->getLogoBase64();

            $pdf = Pdf::loadView('admin.absensi.export-pdf', compact('absensi', 'filters', 'logoBase64'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'defaultFont'       => 'Helvetica',
                    'dpi'               => 96,
                    'isRemoteEnabled'   => true,
                    'isPhpEnabled'      => false,
                    'isFontSubsettingEnabled' => true,
                ]);

            return $pdf->stream('preview-absensi.pdf');

        } catch (\Exception $e) {
            Log::error('PDF Preview Error: ' . $e->getMessage());
            return response('<html><body style="font-family:sans-serif;padding:40px;text-align:center;">'
                . '<h2 style="color:#dc2626;">❌ Gagal Generate PDF</h2>'
                . '<p style="color:#6b7280;">' . htmlspecialchars($e->getMessage()) . '</p>'
                . '<p style="color:#94a3b8;font-size:12px;">Coba pilih rentang tanggal yang lebih pendek (maks 1 bulan).</p>'
                . '</body></html>', 500);
        }
    }

    /**
     * Download PDF
     * GET /admin/absensi/export-pdf
     */
    public function exportPdf(Request $request)
    {
        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(180);

            $absensi = $this->buildPdfQuery($request);
            $filters = $this->getFilters($request);
            
            $logoBase64 = $this->getLogoBase64();

            $pdf = Pdf::loadView('admin.absensi.export-pdf', compact('absensi', 'filters', 'logoBase64'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'defaultFont'       => 'Helvetica',
                    'dpi'               => 96,
                    'isRemoteEnabled'   => true,
                    'isPhpEnabled'      => false,
                    'isFontSubsettingEnabled' => true,
                ]);

            return $pdf->download('laporan-absensi-' . now()->format('Ymd-His') . '.pdf');

        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal download PDF: ' . $e->getMessage());
        }
    }

    /**
     * Preview Data (JSON)
     */
    public function previewData(Request $request)
    {
        $absensi = $this->buildQuery($request)->limit(100)->get();

        $data = $absensi->map(function ($item) {
            $terlambat = '-';
            $tanggalStr = $item->tanggal instanceof \Carbon\Carbon ? $item->tanggal->format('Y-m-d') : $item->tanggal;
            $shift = AbsensiDetectionService::getShiftForDate($item->karyawan, \Carbon\Carbon::parse($tanggalStr));
            
            if ($item->jam_masuk && $shift) {
                $jadwalMasuk = \Carbon\Carbon::parse($tanggalStr . ' ' . $shift->jam_masuk);
                $toleransi = $shift->toleransi_menit ?? 0;
                $diffMenit = \Carbon\Carbon::parse($item->jam_masuk)->diffInMinutes($jadwalMasuk, false);
                
                if ($diffMenit < -$toleransi) {
                    $terlambat = abs($diffMenit) . ' menit';
                } else {
                    $terlambat = 'Tepat Waktu';
                }
            }

            return [
                'id' => $item->id,
                'tanggal' => $tanggalStr,
                'tanggal_formatted' => \Carbon\Carbon::parse($tanggalStr)->format('d/m/Y'),
                'jam_masuk' => $item->jam_masuk,
                'jam_pulang' => $item->jam_pulang,
                'status' => $item->status,
                'face_valid' => $item->face_valid,
                'terlambat' => $terlambat,
                'karyawan' => [
                    'user' => ['nama' => $item->karyawan->user->nama ?? null],
                    'departemen' => ['nama' => $item->karyawan->departemen->nama ?? null],
                    'jabatan' => ['nama_jabatan' => $item->karyawan->jabatan->nama_jabatan ?? null],
                ],
            ];
        });

        $filters = $this->getFilters($request);
        $filterText = [];
        if ($filters['search']) $filterText[] = "Pencarian: {$filters['search']}";
        if ($filters['status'] && $filters['status'] !== 'all') $filterText[] = "Status: {$filters['status']}";
        if ($filters['date_from']) $filterText[] = "Dari: {$filters['date_from']}";
        if ($filters['date_to']) $filterText[] = "Sampai: {$filters['date_to']}";

        return response()->json([
            'data' => $data,
            'total' => $this->buildQuery($request)->count(),
            'filters_text' => count($filterText) ? implode(' · ', $filterText) : 'Semua data',
        ]);
    }

    /**
     * Clear logo cache (jika logo di-update)
     */
    public function clearLogoCache()
    {
        Cache::forget('logo_base64');
        return back()->with('success', 'Logo cache cleared');
    }
}