<?php

namespace App\Http\Controllers\Admin;

use App\Models\Gaji;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GajiController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::with('user')->get();
        $gaji = Gaji::with('karyawan.user')->latest()->get();

        return view('admin.gaji.index', compact('karyawan','gaji'));
    }

    public function create()
    {
        $karyawan = Karyawan::with(['user', 'jabatan'])->get();

        return view('admin.gaji.create', compact('karyawan'));
    }

    public function hitung(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required'
        ]);

        //  cegah dobel hitung
        if (Gaji::where([
            'karyawan_id' => $request->karyawan_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun
        ])->exists()) {
            return back()->with('error','Gaji bulan ini sudah dihitung');
        }

    $karyawan = Karyawan::with('jabatan')->findOrFail($request->karyawan_id);

        // hitung hadir (Hadir & Terlambat dihitung sebagai hari kerja)
        $totalHadir = Absensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        $gajiHarian = $karyawan->jabatan->gaji_harian;
        $totalGaji = $totalHadir * $gajiHarian;

        Gaji::create([
            'karyawan_id' => $karyawan->id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'total_hadir' => $totalHadir,
            'gaji_harian' => $gajiHarian,
            'total_gaji' => $totalGaji,
            'tanggal_hitung' => now()
        ]);

        return back()->with('success','Gaji berhasil dihitung');
    }

    public function slip($id)
    {
        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(180);

            $gaji = Gaji::with([
                'karyawan.user',
                'karyawan.jabatan'
            ])->findOrFail($id);

        $absensi = Absensi::where('karyawan_id', $gaji->karyawan_id)
            ->whereMonth('tanggal', $gaji->bulan)
            ->whereYear('tanggal', $gaji->tahun)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->get();

        $logoBase64 = $this->getLogoBase64();

        $pdf = Pdf::loadView('admin.gaji.slip_pdf', compact('gaji', 'absensi', 'logoBase64'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Helvetica',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

            return $pdf->stream('Slip-Gaji-' . str_replace(' ', '-', $gaji->karyawan->user->nama) . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error previewing slip: ' . $e->getMessage());
            return response('Gagal memuat preview slip gaji. Error: ' . $e->getMessage(), 500);
        }
    }

    public function downloadSlip($id)
    {
        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(180);

            $gaji = Gaji::with([
                'karyawan.user',
                'karyawan.jabatan'
            ])->findOrFail($id);

        $logoBase64 = $this->getLogoBase64();

        $pdf = Pdf::loadView('admin.gaji.slip_pdf', compact('gaji', 'logoBase64'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Helvetica',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

        $fileName = 'Slip-Gaji-' .
            str_replace(' ', '-', $gaji->karyawan->user->nama) . '-' .
            $gaji->bulan . '-' .
            $gaji->tahun . '.pdf';

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error downloading slip: ' . $e->getMessage());
            return back()->with('error', 'Gagal mendownload slip gaji. ' . $e->getMessage());
        }
    }

    public function slipPdf($id)
    {
        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(180);

            $gaji = Gaji::with([
                'karyawan.user',
                'karyawan.jabatan'
            ])->findOrFail($id);

        $absensi = Absensi::where('karyawan_id', $gaji->karyawan_id)
            ->whereMonth('tanggal', $gaji->bulan)
            ->whereYear('tanggal', $gaji->tahun)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->get();

        $logoBase64 = $this->getLogoBase64();

        $pdf = Pdf::loadView('admin.gaji.slip_pdf', compact('gaji', 'absensi', 'logoBase64'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Helvetica',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

        $fileName = 'Slip-Gaji-' . str_replace(' ', '-', $gaji->karyawan->user->nama) . '-' . $gaji->bulan . '-' . $gaji->tahun . '.pdf';

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error exporting slip PDF: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor PDF slip gaji. ' . $e->getMessage());
        }
    }

    /**
     * GET LOGO AS BASE64
     */
    private function getLogoBase64(): ?string
    {
        return Cache::remember('logo_base64', 24 * 60, function () {
            $logoPath = public_path('img/Logo.png');
            if (!file_exists($logoPath)) return null;
            
            try {
                $imageData = file_get_contents($logoPath);
                $base64 = base64_encode($imageData);
                
                // Simplified MIME detection for common formats
                $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                $mimeType = match(strtolower($extension)) {
                    'png'  => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'gif'  => 'image/gif',
                    default => 'image/png'
                };
                
                return 'data:' . $mimeType . ';base64,' . $base64;
            } catch (\Exception $e) {
                Log::error('Error processing logo for slip: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function destroy($id)
    {
        $gaji = Gaji::findOrFail($id);
        $gaji->delete();

        return back()->with('success', 'Data gaji berhasil dihapus');
    }

}
