<?php

namespace App\Http\Controllers\Admin;

use App\Models\HariLiburNasional;
use App\Models\LiburPengganti;
use App\Models\Karyawan;
use App\Services\HolidayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HariLiburNasionalController extends Controller
{
    /**
     * List semua hari libur nasional
     *
     * GET /admin/hari-libur-nasional
     * Query params: tahun, dari, sampai, tipe, nama
     */
    public function index(Request $request)
    {
        $tahun  = $request->get('tahun', now()->year);
        $dari   = $request->get('dari');
        $sampai = $request->get('sampai');
        $tipe   = $request->get('tipe');
        $nama   = $request->get('nama');

        $query = HariLiburNasional::aktif()->orderBy('tanggal');

        // Filter range tanggal (prioritas) atau filter tahun
        if ($dari || $sampai) {
            if ($dari)   $query->whereDate('tanggal', '>=', $dari);
            if ($sampai) $query->whereDate('tanggal', '<=', $sampai);
        } else {
            $query->whereYear('tanggal', $tahun);
        }

        // Filter tipe
        if ($tipe) {
            $query->where('tipe', $tipe);
        }

        // Filter nama (LIKE)
        if ($nama) {
            $query->where('nama', 'like', '%' . $nama . '%');
        }

        $hariLibur = $query->paginate(20)->withQueryString();

        $summary = HariLiburNasional::getSummary();

        return view('admin.hari_libur_nasional.index', [
            'hariLibur' => $hariLibur,
            'tahun'     => $tahun,
            'summary'   => $summary,
        ]);
    }

    /**
     * Show form create hari libur nasional
     *
     * GET /admin/hari-libur-nasional/create
     */
    public function create()
    {
        return view('admin.hari_libur_nasional.create');
    }

    /**
     * Store hari libur nasional
     *
     * POST /admin/hari-libur-nasional
     */
    public function store(Request $request)
    {
        $tipe = $request->get('tipe');

        if ($tipe === 'fixed') {
            // Fixed holiday (recurring)
            $validated = $request->validate([
                'nama' => 'required|string|max:100',
                'bulan' => 'required|integer|between:1,12',
                'hari' => 'required|integer|between:1,31',
                'keterangan' => 'nullable|string',
            ]);

            HariLiburNasional::createFixed(
                $validated['bulan'],
                $validated['hari'],
                $validated['nama'],
                $validated['keterangan']
            );

            $message = "✅ Fixed holiday '{$validated['nama']}' (every {$validated['bulan']}/{$validated['hari']}) created and will auto-recur!";
        } else {
            // Dynamic/Manual holiday (single entry)
            $validated = $request->validate([
                'nama' => 'required|string|max:100',
                'tanggal' => 'required|date|unique:hari_libur_nasional',
                'tipe' => 'in:dynamic,manual',
                'keterangan' => 'nullable|string',
            ]);

            HariLiburNasional::createDynamic(
                $validated['tanggal'],
                $validated['nama'],
                $validated['tipe'],
                $validated['keterangan']
            );

            $message = "✅ {$validated['tipe']} holiday '{$validated['nama']}' ({$validated['tanggal']}) created!";
        }

        return redirect()
            ->route('admin.hari-libur-nasional.index')
            ->with('success', $message);
    }

    /**
     * Show form edit hari libur nasional
     *
     * GET /admin/hari-libur-nasional/{id}/edit
     */
    public function edit(HariLiburNasional $hariLiburNasional)
    {
        return view('admin.hari_libur_nasional.edit', [
            'hariLibur' => $hariLiburNasional,
        ]);
    }

    /**
     * Update hari libur nasional
     *
     * PUT /admin/hari-libur-nasional/{id}
     */
    public function update(Request $request, HariLiburNasional $hariLiburNasional)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:fixed,dynamic,manual',
            'keterangan' => 'nullable|string',
            'is_recurring' => 'boolean',
        ]);

        // If type changed to fixed, or is fixed, ensure bulan_tetap & hari_tetap are set
        if ($validated['tipe'] === 'fixed') {
            $date = Carbon::parse($hariLiburNasional->tanggal);
            $validated['bulan_tetap'] = $date->month;
            $validated['hari_tetap'] = $date->day;
            $validated['is_recurring'] = $request->has('is_recurring');
        } else {
            $validated['bulan_tetap'] = null;
            $validated['hari_tetap'] = null;
            $validated['is_recurring'] = false;
        }

        $hariLiburNasional->update($validated);

        return redirect()
            ->route('admin.hari-libur-nasional.index')
            ->with('success', "✅ Hari libur nasional berhasil diupdate!");
    }

    /**
     * Delete hari libur nasional
     *
     * DELETE /admin/hari-libur-nasional/{id}
     */
    public function destroy(HariLiburNasional $hariLiburNasional)
    {
        $nama = $hariLiburNasional->nama;
        $hariLiburNasional->delete();

        return redirect()
            ->route('admin.hari-libur-nasional.index')
            ->with('success', "✅ Hari libur nasional '{$nama}' berhasil dihapus!");
    }

    /**
     * Auto-generate fixed holidays untuk tahun depan
     *
     * POST /admin/hari-libur-nasional/generate/{tahun}
     */
    public function generateFixedForYear(Request $request, $tahun)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2024|max:2100',
        ]);

        $tahun = $validated['tahun'];

        // Auto-generate fixed holidays
        $created = HariLiburNasional::autoGenerateFixedForYear($tahun);

        return redirect()
            ->back()
            ->with('success', "✅ Auto-generated " . count($created) . " fixed holidays untuk tahun {$tahun}!");
    }

    /**
     * View saldo libur pengganti semua karyawan
     *
     * GET /admin/libur-pengganti
     */
    public function indexLiburPengganti()
    {
        $liburPengganti = LiburPengganti::with('karyawan.user')
            ->orderBy('saldo', 'desc')
            ->paginate(20);

        return view('admin.libur_pengganti.index', [
            'liburPengganti' => $liburPengganti,
        ]);
    }

    /**
     * Adjust saldo libur pengganti (admin manual)
     *
     * POST /admin/libur-pengganti/{karyawanId}/adjust
     */
    public function adjustSaldo(Request $request, $karyawanId)
    {
        $karyawan = Karyawan::findOrFail($karyawanId);

        $validated = $request->validate([
            'saldo_baru' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $libur = LiburPengganti::getOrCreate($karyawanId);
        $saldoLama = $libur->saldo;

        $libur->saldo = $validated['saldo_baru'];
        $libur->terakhir_diupdate = now();
        $libur->save();

        return redirect()
            ->route('admin.libur-pengganti.index')
            ->with('success', "✅ Saldo libur pengganti {$karyawan->user->nama} diubah dari {$saldoLama} → {$validated['saldo_baru']}!");
    }

    /**
     * Reset saldo semua karyawan (untuk tahun baru)
     *
     * POST /admin/libur-pengganti/reset-all
     */
    public function resetAllSaldo(Request $request)
    {
        $validated = $request->validate([
            'confirm' => 'required|accepted',
        ]);

        $count = 0;
        foreach (Karyawan::all() as $karyawan) {
            LiburPengganti::resetSaldo($karyawan->id);
            $count++;
        }

        return redirect()
            ->route('admin.libur-pengganti.index')
            ->with('success', "✅ Saldo libur pengganti {$count} karyawan direset menjadi 0!");
    }

    /**
     * API: Get saldo libur pengganti untuk karyawan
     *
     * GET /api/admin/libur-pengganti/{karyawanId}
     */
    public function getSaldo($karyawanId)
    {
        $saldo = LiburPengganti::getSaldo($karyawanId);

        return response()->json([
            'karyawan_id' => $karyawanId,
            'saldo' => $saldo,
        ]);
    }

    /**
     * Sync holidays from external API
     *
     * POST /admin/hari-libur-nasional/sync
     */
    public function syncHolidays(Request $request, HolidayService $holidayService)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:2100',
            'country_code' => 'required|string|size:2',
        ]);

        $tahun = $validated['tahun'];
        $countryCode = $validated['country_code'];

        $holidays = $holidayService->getHolidays($tahun, $countryCode);

        if (empty($holidays)) {
            return redirect()->back()->with('error', "❌ Gagal mengambil data libur untuk tahun {$tahun} ({$countryCode}) atau data tidak tersedia.");
        }

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($holidays as $h) {
            $tanggal = $h['date'];
            $nama = $h['localName'] ?? $h['name'];

            // Check if already exists
            $exists = HariLiburNasional::whereDate('tanggal', $tanggal)->exists();

            if (!$exists) {
                $description = $h['description'] ?? "Synced from API ({$countryCode})";
                
                HariLiburNasional::create([
                    'tanggal' => $tanggal,
                    'nama' => $nama,
                    'tipe' => 'dynamic',
                    'tahun' => $tahun,
                    'is_recurring' => false,
                    'keterangan' => $description,
                    'is_active' => true,
                ]);
                $createdCount++;
            } else {
                $skippedCount++;
            }
        }

        return redirect()
            ->route('admin.hari-libur-nasional.index', ['tahun' => $tahun])
            ->with('success', "✅ Berhasil sinkronisasi: {$createdCount} data baru ditambahkan, {$skippedCount} data sudah ada.");
    }
}
