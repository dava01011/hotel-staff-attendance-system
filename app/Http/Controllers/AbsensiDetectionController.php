<?php

namespace App\Http\Controllers;

use App\Services\AbsensiDetectionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiDetectionController extends Controller
{
    /**
     * Show form untuk detect absent
     *
     * GET /admin/absensi/detect-form
     */
    public function showForm()
    {
        return view('admin.absensi.detect_absent_form');
    }

    /**
     * Manual trigger detect absent
     *
     * POST /admin/absensi/detect-absent
     */
    public function detectAbsent(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'mode' => 'in:single,range', // single date atau date range
        ]);

        try {
            $mode = $validated['mode'] ?? 'single';

            if ($mode === 'single') {
                // Detect for single date
                $date = $validated['date']
                    ? Carbon::createFromFormat('Y-m-d', $validated['date'])
                    : Carbon::yesterday();

                $result = AbsensiDetectionService::detectAbsentForDate($date);

                return back()
                    ->with('success', "✅ Detect absent selesai untuk {$date->format('Y-m-d')}")
                    ->with('result', $result);

            } else {
                // Detect for date range
                $startDate = Carbon::createFromFormat('Y-m-d', $validated['start_date']);
                $endDate = Carbon::createFromFormat('Y-m-d', $validated['end_date']);

                if ($startDate > $endDate) {
                    return back()->withErrors('Start date harus lebih awal dari end date');
                }

                $result = AbsensiDetectionService::detectAbsentForDateRange($startDate, $endDate);

                return back()
                    ->with('success', "✅ Detect absent selesai untuk {$startDate->format('Y-m-d')} - {$endDate->format('Y-m-d')}")
                    ->with('result', $result);
            }

        } catch (\Exception $e) {
            return back()
                ->withErrors("Error: " . $e->getMessage());
        }
    }

    /**
     * API endpoint untuk get hasil detection
     *
     * GET /api/absensi/detection-result
     */
    public function getDetectionResult()
    {
        $result = session('result');

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'No result found',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Test command (bisa jalankan manual)
     *
     * GET /admin/absensi/test-detect
     */
    public function testDetect()
    {
        // Test untuk hari kemarin
        $date = Carbon::yesterday();

        $result = AbsensiDetectionService::detectAbsentForDate($date);

        return response()->json([
            'message' => "Test detect absent untuk {$date->format('Y-m-d')}",
            'result' => $result,
        ]);
    }
}
