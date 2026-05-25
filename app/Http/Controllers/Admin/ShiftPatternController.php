<?php

namespace App\Http\Controllers\Admin;

use App\Models\Karyawan;
use App\Models\KaryawanShiftPattern;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ShiftPatternController extends Controller
{
    /**
     * Show list karyawan & shift pattern mereka
     *
     * GET /admin/shift-pattern
     */
    public function index()
    {
        $query = Karyawan::with('user', 'shiftPatterns.shift');
        
        // Filter Departemen untuk Admin
        if (auth()->user()->role === 'admin' && auth()->user()->karyawan) {
            $query->where('departemen_id', auth()->user()->karyawan->departemen_id);
        }

        $karyawan = $query->get();

        return view('admin.shift_pattern.index', [
            'karyawan' => $karyawan,
        ]);
    }

    /**
     * Show form untuk edit default pattern
     *
     * GET /admin/shift-pattern/{karyawanId}/default/edit
     */
    public function editDefaultForm($karyawanId)
    {
        $karyawan = Karyawan::with('user')->findOrFail($karyawanId);

        // Security Check
        if (auth()->user()->role === 'admin' && auth()->user()->karyawan) {
            if ($karyawan->departemen_id != auth()->user()->karyawan->departemen_id) {
                abort(403, 'Unauthorized access to this employee.');
            }
        }

        // Get current default pattern
        $defaultPattern = KaryawanShiftPattern::getDefaultPattern($karyawanId);
        
        // Get all available shifts
        $shifts = \App\Models\Shift::all();

        return view('admin.shift_pattern.edit_default', [
            'karyawan' => $karyawan,
            'pattern' => $defaultPattern,
            'shifts' => $shifts,
        ]);
    }

    /**
     * Update default pattern
     *
     * POST /admin/shift-pattern/{karyawanId}/default/update
     */
    public function updateDefault(Request $request, $karyawanId)
    {
        $karyawan = Karyawan::findOrFail($karyawanId);

        // Validate
        $validated = $request->validate([
            'hari' => 'required|array',
            'hari.*' => 'in:minggu,senin,selasa,rabu,kamis,jumat,sabtu',
            'tipe' => 'required|array',
            'tipe.*' => 'in:kerja,libur',
            'shift_id' => 'nullable|array',
            'shift_id.*' => 'nullable|exists:shift,id',
        ]);

        // Build pattern array
        $patterns = [];
        foreach ($validated['hari'] as $index => $hari) {
            $patterns[$hari] = [
                'tipe' => $validated['tipe'][$index],
                'shift_id' => $validated['tipe'][$index] === 'kerja' ? ($validated['shift_id'][$hari] ?? null) : null,
            ];
        }

        // Update pattern
        KaryawanShiftPattern::setDefaultPattern($karyawanId, $patterns);

        return redirect()
            ->route('admin.shift-pattern.index')
            ->with('success', "✅ Default pattern untuk {$karyawan->user->nama} berhasil diupdate!");
    }

    /**
     * Show form untuk edit weekly pattern (override)
     *
     * GET /admin/shift-pattern/{karyawanId}/weekly/edit?minggu={minggu_ke}&tahun={tahun}
     */
    public function editWeeklyForm($karyawanId, Request $request)
    {
        $karyawan = Karyawan::with('user')->findOrFail($karyawanId);

        // Security Check
        if (auth()->user()->role === 'admin' && auth()->user()->karyawan) {
            if ($karyawan->departemen_id != auth()->user()->karyawan->departemen_id) {
                abort(403, 'Unauthorized access to this employee.');
            }
        }

        $mingguKe = $request->get('minggu_ke', now()->weekOfYear);
        $tahun = $request->get('tahun', now()->year);

        // Get weekly pattern (jika ada)
        $weeklyPattern = KaryawanShiftPattern::weekly($karyawanId, $mingguKe, $tahun)
            ->get()
            ->keyBy('hari');

        // Get default pattern sebagai fallback
        $defaultPattern = KaryawanShiftPattern::getDefaultPattern($karyawanId);

        // Get week date range
        $weekRange = KaryawanShiftPattern::getWeekDateRange($mingguKe, $tahun);
        
        // Get all available shifts
        $shifts = \App\Models\Shift::all();

        return view('admin.shift_pattern.edit_weekly', [
            'karyawan' => $karyawan,
            'mingguKe' => $mingguKe,
            'tahun' => $tahun,
            'weeklyPattern' => $weeklyPattern,
            'defaultPattern' => $defaultPattern,
            'weekRange' => $weekRange,
            'shifts' => $shifts,
        ]);
    }

    /**
     * Update atau create weekly pattern (override)
     *
     * POST /admin/shift-pattern/{karyawanId}/weekly/update
     */
    public function updateWeekly(Request $request, $karyawanId)
    {
        $karyawan = Karyawan::findOrFail($karyawanId);

        // Validate
        $validated = $request->validate([
            'minggu_ke' => 'required|integer|between:1,52',
            'tahun' => 'required|integer|min:2020',
            'hari' => 'required|array',
            'hari.*' => 'in:minggu,senin,selasa,rabu,kamis,jumat,sabtu',
            'tipe' => 'required|array',
            'tipe.*' => 'in:kerja,libur',
            'shift_id' => 'nullable|array',
            'shift_id.*' => 'nullable|exists:shift,id',
        ]);

        // Build pattern array
        $patterns = [];
        foreach ($validated['hari'] as $index => $hari) {
            $patterns[$hari] = [
                'tipe' => $validated['tipe'][$index],
                'shift_id' => $validated['tipe'][$index] === 'kerja' ? ($validated['shift_id'][$hari] ?? null) : null,
            ];
        }

        // Set weekly override
        KaryawanShiftPattern::setWeeklyOverride(
            $karyawanId,
            $validated['minggu_ke'],
            $validated['tahun'],
            $patterns
        );

        $weekRange = KaryawanShiftPattern::getWeekDateRange($validated['minggu_ke'], $validated['tahun']);

        return redirect()
            ->route('admin.shift-pattern.index')
            ->with('success', "✅ Weekly pattern untuk {$karyawan->user->nama} minggu {$validated['minggu_ke']}/{$validated['tahun']} ({$weekRange['start']->format('d-m-Y')} s/d {$weekRange['end']->format('d-m-Y')}) berhasil diupdate!");
    }

    /**
     * Delete weekly override pattern
     *
     * DELETE /admin/shift-pattern/{karyawanId}/weekly/delete
     */
    public function deleteWeekly(Request $request, $karyawanId)
    {
        $karyawan = Karyawan::findOrFail($karyawanId);

        // Validate
        $validated = $request->validate([
            'minggu_ke' => 'required|integer|between:1,52',
            'tahun' => 'required|integer|min:2020',
        ]);

        // Delete weekly pattern
        KaryawanShiftPattern::where('karyawan_id', $karyawanId)
            ->where('minggu_ke', $validated['minggu_ke'])
            ->where('tahun', $validated['tahun'])
            ->delete();

        $weekRange = KaryawanShiftPattern::getWeekDateRange($validated['minggu_ke'], $validated['tahun']);

        return redirect()
            ->route('admin.shift-pattern.index')
            ->with('success', "✅ Weekly override untuk {$karyawan->user->nama} minggu {$validated['minggu_ke']}/{$validated['tahun']} dihapus. Kembali ke default pattern!");
    }

    /**
     * API: Get pattern untuk tanggal tertentu
     *
     * GET /api/shift-pattern/{karyawanId}?date=2026-03-15
     */
    public function getPatternForDate($karyawanId)
    {
        $karyawan = Karyawan::findOrFail($karyawanId);

        // Security Check
        if (auth()->user()->role === 'admin' && auth()->user()->karyawan) {
            if ($karyawan->departemen_id != auth()->user()->karyawan->departemen_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $date = request('date') ? Carbon::createFromFormat('Y-m-d', request('date')) : now();

        $pattern = KaryawanShiftPattern::getPatternForDate($karyawanId, $date);

        return response()->json([
            'karyawan_id' => $karyawanId,
            'date' => $date->toDateString(),
            'minggu_ke' => $date->weekOfYear,
            'tahun' => $date->year,
            'is_weekly_override' => KaryawanShiftPattern::hasWeeklyOverride($karyawanId, $date->weekOfYear, $date->year),
            'pattern' => $pattern->map(function ($p) {
                return [
                    'hari' => $p->hari,
                    'label' => KaryawanShiftPattern::getLabelHari($p->hari),
                    'tipe' => $p->tipe,
                ];
            })->values(),
        ]);
    }

    /**
     * Show calendar view untuk visualisasi pattern
     *
     * GET /admin/shift-pattern/{karyawanId}/calendar?tahun=2026
     */
    public function calendar($karyawanId, Request $request)
    {
        $karyawan = Karyawan::with('user')->findOrFail($karyawanId);
        $tahun = $request->get('tahun', now()->year);

        // Get all weekly overrides untuk tahun ini
        $weeklyOverrides = KaryawanShiftPattern::where('karyawan_id', $karyawanId)
            ->whereNotNull('minggu_ke')
            ->where('tahun', $tahun)
            ->get()
            ->groupBy('minggu_ke');

        // Get default pattern
        $defaultPattern = KaryawanShiftPattern::getDefaultPattern($karyawanId);

        return view('admin.shift_pattern.calendar', [
            'karyawan' => $karyawan,
            'tahun' => $tahun,
            'weeklyOverrides' => $weeklyOverrides,
            'defaultPattern' => $defaultPattern,
        ]);
    }
}
