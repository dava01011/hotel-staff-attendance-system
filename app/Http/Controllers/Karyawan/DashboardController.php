<?php

namespace App\Http\Controllers\Karyawan;

use Carbon\Carbon;
use App\Models\Cuti;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\AjukanShift;
use App\Models\Pengumuman;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Get shift aktif untuk karyawan berdasarkan tanggal
     */
    private function getActiveShift($karyawan, $tanggal)
    {
        $departemen_id = $karyawan->departemen_id;

        // 1. Cek apakah ada pengajuan shift SEMENTARA yang disetujui dan masih aktif
        $shiftSementara = AjukanShift::where('departemen_id', $departemen_id)
            ->where('jenis', 'sementara')
            ->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->with('shiftBaru')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($shiftSementara) {
            return $shiftSementara->shiftBaru;
        }

        // 2. Jika tidak ada shift sementara, ambil shift dari shift pattern
        $date = Carbon::parse($tanggal);
        $namaHari = \App\Models\KaryawanShiftPattern::getNamaHari($date);
        $pattern = \App\Models\KaryawanShiftPattern::getPatternForDate($karyawan->id, $date);

        if ($pattern && isset($pattern[$namaHari]) && $pattern[$namaHari]->tipe === 'kerja' && $pattern[$namaHari]->shift_id) {
            return \App\Models\Shift::find($pattern[$namaHari]->shift_id);
        }

        return null;
    }

    /**
     * Display dashboard
     */
    public function index()
    {
        $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();
        $today = Carbon::now()->toDateString();

        // Get shift aktif hari ini (bisa default atau sementara)
        $shiftData = $this->getActiveShift($karyawan, $today);

        // Absensi hari ini
        $absensiToday = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Jika tidak ada absensi hari ini, cek kemarin untuk shift lintas hari
        if (!$absensiToday && $shiftData && $shiftData->lintas_hari) {
            $yesterday = Carbon::yesterday()->toDateString();
            $yesterdayShift = $this->getActiveShift($karyawan, $yesterday);

            if ($yesterdayShift && $yesterdayShift->lintas_hari) {
                $absensiToday = Absensi::where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $yesterday)
                    ->first();
            }
        }

        // Statistik bulan ini
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $stats = [
            'hadir' => Absensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status', 'hadir')
                ->count(),

            'terlambat' => Absensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status', 'terlambat')
                ->count(),

            'alpa' => Absensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status', 'alpha')
                ->count(),
        ];

        // ========== TAMBAHAN: CALENDAR DATA ==========
        $calendarData = $this->generateCalendarData($karyawan->id);

        // Fetch Pengumuman (Global or Department specific)
        $pengumuman = Pengumuman::where('tipe', 'global')
            ->orWhere(function($q) use ($karyawan) {
                $q->where('tipe', 'departemen')
                  ->where('departemen_id', $karyawan->departemen_id);
            })
            ->latest()
            ->take(3)
            ->get();

        // Jika shift tidak ada, set default values
        if (!$shiftData) {
            return view('karyawan.dashboard', [
                'shift' => 'Belum Ditentukan',
                'jamMasuk' => '--:--',
                'jamPulang' => '--:--',
                'toleransi' => 0,
                'lintasHari' => false,
                'absensiToday' => $absensiToday,
                'stats' => $stats,
                'calendarData' => $calendarData,
                'pengumuman' => $pengumuman
            ])->with('warning', 'Shift Anda belum ditentukan. Silakan hubungi admin departemen Anda.');
        }

        // Return view dengan data lengkap
        return view('karyawan.dashboard', [
            'shift' => $shiftData->kode,
            'jamMasuk' => substr($shiftData->jam_masuk, 0, 5),
            'jamPulang' => substr($shiftData->jam_pulang, 0, 5),
            'toleransi' => $shiftData->toleransi_menit,
            'lintasHari' => $shiftData->lintas_hari,
            'absensiToday' => $absensiToday,
            'stats' => $stats,
            'calendarData' => $calendarData,
            'pengumuman' => $pengumuman
        ]);
    }

    /**
     * ========== BARU: Generate calendar data untuk mini calendar ==========
     * Return: Array dengan key tanggal (Y-m-d) dan value status
     */
    private function generateCalendarData($karyawanId, $month = null, $year = null)
    {
        $calendarData = [];

        if ($month && $year) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        } else {
            // Ambil 3 bulan: bulan lalu, bulan ini, bulan depan
            $startDate = now()->startOfMonth()->subMonth();
            $endDate = now()->endOfMonth()->addMonth();
        }

        // 1. Ambil data absensi
        $absensi = Absensi::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        // 2. Ambil data cuti yang disetujui
        $cuti = Cuti::where('karyawan_id', $karyawanId)
            ->where('status', 'disetujui')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_mulai', [$startDate, $endDate])
                      ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('tanggal_mulai', '<=', $startDate)
                            ->where('tanggal_selesai', '>=', $endDate);
                      });
            })
            ->get();

        // 3. Ambil libur nasional
        $liburNasional = \App\Models\HariLiburNasional::aktif()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        // 4. Ambil shift pattern (Default)
        $defaultPattern = \App\Models\KaryawanShiftPattern::getDefaultPattern($karyawanId);

        // 5. Ambil shift pattern (Weekly Overrides)
        $weeklyPatterns = \App\Models\KaryawanShiftPattern::where('karyawan_id', $karyawanId)
            ->where('is_active', true)
            ->where('is_default', false)
            ->whereIn('tahun', [$startDate->year, $endDate->copy()->subWeek()->year, $endDate->year])
            ->get()
            ->groupBy(function($item) {
                return $item->tahun . '-' . $item->minggu_ke;
            });

        // Loop setiap hari dalam range
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $namaHari = \App\Models\KaryawanShiftPattern::getNamaHari($current);
            $mingguKe = $current->weekOfYear;
            $tahun = $current->year;
            
            // Check shift pattern
            $isLiburShift = false;
            $weeklyKey = $tahun . '-' . $mingguKe;
            if (isset($weeklyPatterns[$weeklyKey]) && $weeklyPatterns[$weeklyKey]->where('hari', $namaHari)->first()) {
                $pattern = $weeklyPatterns[$weeklyKey]->where('hari', $namaHari)->first();
                $isLiburShift = ($pattern->tipe === 'libur');
            } else if (isset($defaultPattern[$namaHari])) {
                $isLiburShift = ($defaultPattern[$namaHari]->tipe === 'libur');
            } else {
                $isLiburShift = ($current->dayOfWeek === 0); // Default minggu libur
            }

            // Priority:
            // 1. Absensi
            // 2. Cuti
            // 3. Libur Nasional
            // 4. Shift Libur

            if (isset($absensi[$dateStr])) {
                $status = $absensi[$dateStr]->status;
                // Map status ke calendar class
                switch($status) {
                    case 'hadir':
                        $calendarData[$dateStr] = 'hadir';
                        break;
                    case 'terlambat':
                        $calendarData[$dateStr] = 'terlambat';
                        break;
                    case 'alpha':
                    case 'alpa':
                        $calendarData[$dateStr] = 'alpa';
                        break;
                    case 'sakit':
                        $calendarData[$dateStr] = 'sakit';
                        break;
                    case 'izin':
                        $calendarData[$dateStr] = 'izin';
                        break;
                    case 'cuti':
                        $calendarData[$dateStr] = 'cuti';
                        break;
                    default:
                        $calendarData[$dateStr] = $status;
                }
            } else {
                $isCuti = false;
                foreach ($cuti as $c) {
                    if ($current->between(Carbon::parse($c->tanggal_mulai)->startOfDay(), Carbon::parse($c->tanggal_selesai)->endOfDay())) {
                        $calendarData[$dateStr] = 'cuti';
                        $isCuti = true;
                        break;
                    }
                }

                if (!$isCuti) {
                    if (isset($liburNasional[$dateStr])) {
                        $calendarData[$dateStr] = 'libur'; // libur nasional
                    } else if ($isLiburShift) {
                        $calendarData[$dateStr] = 'libur'; // shift libur
                    }
                }
            }

            $current->addDay();
        }

        return $calendarData;
    }

    /**
     * ========== BARU (OPTIONAL): Get calendar data via AJAX ==========
     * Untuk load calendar dinamis saat user ganti bulan
     */
    public function getCalendarData(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();

        $calendarData = $this->generateCalendarData($karyawan->id, $month, $year);

        return response()->json($calendarData);
    }
}
