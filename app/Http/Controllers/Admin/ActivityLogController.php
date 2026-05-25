<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by search (user name)
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get paginated logs
        $logs = $query->latest()->paginate(20);

        // Calculate stats
        $stats = [
            'total'   => ActivityLog::count(),
            'create'  => ActivityLog::where('action', 'create')->count(),
            'update'  => ActivityLog::where('action', 'update')->count(),
            'delete'  => ActivityLog::where('action', 'delete')->count(),
            'approve' => ActivityLog::where('action', 'approve')->count(),
            'reject'  => ActivityLog::where('action', 'reject')->count(),
        ];

        return view('admin.activity-log.index', compact('logs', 'stats'));
    }

    /**
     * Show delete modal with options
     */
    public function showDeleteForm()
    {
        return view('admin.activity-log.delete-modal');
    }

    /**
     * Delete logs by specific date
     */
    public function deleteByDate(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ], [
            'tanggal.required' => 'Tanggal wajib dipilih',
            'tanggal.date'     => 'Format tanggal tidak valid',
        ]);

        $tanggal = $request->tanggal;
        $deleted = ActivityLog::whereDate('created_at', $tanggal)->delete();

        return response()->json([
            'success' => true,
            'message' => "✅ Berhasil menghapus {$deleted} log pada tanggal " . Carbon::parse($tanggal)->format('d M Y'),
            'deleted' => $deleted,
        ]);
    }

    /**
     * Delete logs by month & year
     */
    public function deleteByMonth(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date_format:Y-m',
        ], [
            'bulan.required'      => 'Bulan wajib dipilih',
            'bulan.date_format'   => 'Format bulan tidak valid (YYYY-MM)',
        ]);

        $bulan = $request->bulan;
        list($tahun, $bln) = explode('-', $bulan);

        $deleted = ActivityLog::whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bln)
            ->delete();

        $namaBulan = Carbon::createFromDate($tahun, $bln, 1)->translatedFormat('F Y');

        return response()->json([
            'success' => true,
            'message' => "✅ Berhasil menghapus {$deleted} log pada bulan {$namaBulan}",
            'deleted' => $deleted,
        ]);
    }

    /**
     * Delete logs by year
     */
    public function deleteByYear(Request $request)
    {
        $request->validate([
            'tahun' => 'required|digits:4|numeric|min:2000|max:2099',
        ], [
            'tahun.required' => 'Tahun wajib dipilih',
            'tahun.digits'   => 'Tahun harus 4 digit',
            'tahun.min'      => 'Tahun minimal 2000',
            'tahun.max'      => 'Tahun maksimal 2099',
        ]);

        $tahun = $request->tahun;
        $deleted = ActivityLog::whereYear('created_at', $tahun)->delete();

        return response()->json([
            'success' => true,
            'message' => "✅ Berhasil menghapus {$deleted} log pada tahun {$tahun}",
            'deleted' => $deleted,
        ]);
    }

    /**
     * Delete logs by date range
     */
    public function deleteByRange(Request $request)
    {
        $request->validate([
            'dari_tanggal'  => 'required|date',
            'sampai_tanggal' => 'required|date|after_or_equal:dari_tanggal',
        ], [
            'dari_tanggal.required'      => 'Tanggal awal wajib dipilih',
            'dari_tanggal.date'          => 'Format tanggal awal tidak valid',
            'sampai_tanggal.required'    => 'Tanggal akhir wajib dipilih',
            'sampai_tanggal.date'        => 'Format tanggal akhir tidak valid',
            'sampai_tanggal.after_or_equal' => 'Tanggal akhir harus lebih besar atau sama dengan tanggal awal',
        ]);

        $dariTanggal = $request->dari_tanggal;
        $sampaiTanggal = $request->sampai_tanggal;

        $deleted = ActivityLog::whereBetween('created_at', [
            $dariTanggal . ' 00:00:00',
            $sampaiTanggal . ' 23:59:59',
        ])->delete();

        $formatDari = Carbon::parse($dariTanggal)->format('d M Y');
        $formatSampai = Carbon::parse($sampaiTanggal)->format('d M Y');

        return response()->json([
            'success' => true,
            'message' => "✅ Berhasil menghapus {$deleted} log dari {$formatDari} sampai {$formatSampai}",
            'deleted' => $deleted,
        ]);
    }

    /**
     * Delete logs older than X days
     */
    public function deleteOlderThan(Request $request)
    {
        $request->validate([
            'hari' => 'required|integer|min:1|max:3650',
        ], [
            'hari.required' => 'Jumlah hari wajib diisi',
            'hari.integer'  => 'Jumlah hari harus angka',
            'hari.min'      => 'Jumlah hari minimal 1',
            'hari.max'      => 'Jumlah hari maksimal 3650 (10 tahun)',
        ]);

        $hari = $request->hari;
        $cutoffDate = now()->subDays($hari);

        $deleted = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        return response()->json([
            'success' => true,
            'message' => "✅ Berhasil menghapus {$deleted} log yang lebih lama dari {$hari} hari (sebelum " . $cutoffDate->format('d M Y') . ")",
            'deleted' => $deleted,
        ]);
    }

    /**
     * Delete logs by module
     */
    public function deleteByModule(Request $request)
    {
        $request->validate([
            'module' => 'required|string|in:cuti,absensi,shift,jadwal_shift,karyawan,user,wajah,gaji,approval',
        ], [
            'module.required' => 'Module wajib dipilih',
            'module.in'       => 'Module tidak valid',
        ]);

        $module = $request->module;
        $deleted = ActivityLog::where('module', $module)->delete();

        return response()->json([
            'success' => true,
            'message' => "✅ Berhasil menghapus {$deleted} log dari module " . ucfirst($module),
            'deleted' => $deleted,
        ]);
    }

    /**
     * Delete logs by action type
     */
    public function deleteByAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:create,update,delete,approve,reject',
        ], [
            'action.required' => 'Tipe aksi wajib dipilih',
            'action.in'       => 'Tipe aksi tidak valid',
        ]);

        $action = $request->action;
        $deleted = ActivityLog::where('action', $action)->delete();

        return response()->json([
            'success' => true,
            'message' => "✅ Berhasil menghapus {$deleted} log dengan aksi " . ucfirst($action),
            'deleted' => $deleted,
        ]);
    }

    /**
     * Delete all logs (dengan konfirmasi)
     */
    public function deleteAll(Request $request)
    {
        $request->validate([
            'confirm' => 'required|accepted',
        ], [
            'confirm.required' => 'Konfirmasi wajib dicentang',
            'confirm.accepted' => 'Anda harus menerima konfirmasi',
        ]);

        $totalCount = ActivityLog::count();
        ActivityLog::truncate();

        return response()->json([
            'success' => true,
            'message' => "✅ Berhasil menghapus semua {$totalCount} log dari sistem",
            'deleted' => $totalCount,
        ]);
    }

    /**
     * Clear old logs (older than X days) - Batch operation
     */
    public function clearOldLogs()
    {
        // Hapus log lebih dari 90 hari
        $deleted = ActivityLog::where('created_at', '<', now()->subDays(90))->delete();

        return back()->with('success', "✅ Berhasil menghapus {$deleted} log yang lebih dari 90 hari lalu");
    }

    /**
     * Get statistics for delete confirmation
     */
    public function getDeleteStats(Request $request)
    {
        $type = $request->query('type');
        $query = ActivityLog::query();

        switch ($type) {
            case 'date':
                $date = $request->query('date');
                if ($date) {
                    $query->whereDate('created_at', $date);
                }
                break;

            case 'month':
                $month = $request->query('month');
                if ($month) {
                    list($tahun, $bln) = explode('-', $month);
                    $query->whereYear('created_at', $tahun)
                          ->whereMonth('created_at', $bln);
                }
                break;

            case 'year':
                $year = $request->query('year');
                if ($year) {
                    $query->whereYear('created_at', $year);
                }
                break;

            case 'range':
                $from = $request->query('dari_tanggal');
                $to = $request->query('sampai_tanggal');
                if ($from && $to) {
                    $query->whereBetween('created_at', [
                        $from . ' 00:00:00',
                        $to . ' 23:59:59',
                    ]);
                }
                break;

            case 'older':
                $days = $request->query('hari', 90);
                $query->where('created_at', '<', now()->subDays($days));
                break;

            case 'module':
                $module = $request->query('module');
                if ($module) {
                    $query->where('module', $module);
                }
                break;

            case 'action':
                $action = $request->query('action');
                if ($action) {
                    $query->where('action', $action);
                }
                break;
        }

        $count = $query->count();
        $oldestDate = $query->min('created_at');
        $newestDate = $query->max('created_at');

        return response()->json([
            'count' => $count,
            'oldest_date' => $oldestDate ? Carbon::parse($oldestDate)->format('d M Y, H:i') : null,
            'newest_date' => $newestDate ? Carbon::parse($newestDate)->format('d M Y, H:i') : null,
        ]);
    }
}