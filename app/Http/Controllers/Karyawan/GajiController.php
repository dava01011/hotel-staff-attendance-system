<?php

namespace App\Http\Controllers\Karyawan;

use App\Models\Gaji;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class GajiController extends Controller
{
    /**
     * Display list of gaji with filters
     */
    public function index(Request $request)
    {
        $karyawanId = Auth::user()->karyawan->id;
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $query = Gaji::where('karyawan_id', $karyawanId);

        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        $gaji = $query->orderByDesc('tahun')
                      ->orderByDesc('bulan')
                      ->get();

        return view('karyawan.gaji.index', compact('gaji', 'tahun', 'bulan'));
    }

    /**
     * Display detail slip gaji (HTML view)
     */
    public function slip($id)
    {
        $karyawanId = Auth::user()->karyawan->id;

        $gaji = Gaji::where('id', $id)
            ->where('karyawan_id', $karyawanId)
            ->with(['karyawan.user', 'karyawan.jabatan'])
            ->firstOrFail();

        return view('karyawan.gaji.slip', compact('gaji'));
    }

    /**
     * Generate & preview PDF inline (stream dalam browser)
     */
    public function preview($id)
    {
        $karyawanId = Auth::user()->karyawan->id;

        $gaji = Gaji::where('id', $id)
            ->where('karyawan_id', $karyawanId)
            ->with(['karyawan.user', 'karyawan.jabatan'])
            ->firstOrFail();

        try {
            $pdf = Pdf::loadView('karyawan.gaji.slip_pdf', compact('gaji'))
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'defaultFont'  => 'DejaVu Sans',
                    'dpi'          => 150,
                    'isRemoteEnabled' => false,
                ]);

            return $pdf->stream('slip-gaji.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download PDF file
     */
    public function download($id)
    {
        $karyawanId = Auth::user()->karyawan->id;

        $gaji = Gaji::where('id', $id)
            ->where('karyawan_id', $karyawanId)
            ->with(['karyawan.user', 'karyawan.jabatan'])
            ->firstOrFail();

        try {
            $bulanNama = \Carbon\Carbon::create()->month($gaji->bulan)->locale('id')->monthName;
            $filename = 'Slip-Gaji-' . $bulanNama . '-' . $gaji->tahun . '.pdf';

            $pdf = Pdf::loadView('karyawan.gaji.slip_pdf', compact('gaji'))
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'defaultFont'  => 'DejaVu Sans',
                    'dpi'          => 150,
                    'isRemoteEnabled' => false,
                ]);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal download PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PDF stats (untuk preview count)
     */
    public function getPdfStats($id)
    {
        $karyawanId = Auth::user()->karyawan->id;

        $gaji = Gaji::where('id', $id)
            ->where('karyawan_id', $karyawanId)
            ->firstOrFail();

        $bulanNama = \Carbon\Carbon::create()->month($gaji->bulan)->locale('id')->monthName;

        return response()->json([
            'bulan' => $bulanNama,
            'tahun' => $gaji->tahun,
            'nama_karyawan' => $gaji->karyawan->user->nama,
            'jabatan' => $gaji->karyawan->jabatan->nama_jabatan ?? 'N/A',
            'total_gaji' => number_format($gaji->total_gaji, 0, ',', '.'),
        ]);
    }
}