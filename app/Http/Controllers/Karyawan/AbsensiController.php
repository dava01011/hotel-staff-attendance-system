<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AjukanShift;
use App\Models\Cuti;
use App\Models\HariLiburNasional;

use App\Models\Karyawan;
use App\Models\LiburPengganti;
use App\Models\LokasiKantor;
use App\Models\Notifikasi;
use App\Models\WajahKaryawan;
use App\Services\AbsensiDetectionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AbsensiController extends Controller
{
    /**
     * Show form absen masuk
     * UPDATED: Pass lokasi kantor ke view
     */
    public function masukForm()
    {
        $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();

        // Cek apakah sudah absen masuk hari ini
        $today = date('Y-m-d');
        $existingAbsen = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existingAbsen && $existingAbsen->jam_masuk) {
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Anda sudah absen masuk hari ini!');
        }

        // Ambil data wajah karyawan
        $wajahKaryawan = WajahKaryawan::where('karyawan_id', $karyawan->id)->first();

        if (!$wajahKaryawan) {
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Wajah Anda belum terdaftar! Pergi ke halaman pengaturan atau Hubungi admin untuk registrasi.');
        }

        // Get shift aktif
        $shift = $this->getActiveShift($karyawan, $today);

        if (!$shift) {
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Shift departemen Anda belum ditentukan. Hubungi admin!');
        }

        // ===== TAMBAHAN: Get lokasi kantor dari database =====
        $lokasiKantor = LokasiKantor::first();

        if (!$lokasiKantor) {
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Lokasi kantor belum dikonfigurasi! Hubungi admin.');
        }

        return view('karyawan.absensi.masuk-form', compact('wajahKaryawan', 'shift', 'lokasiKantor'));
    }

        public function index()
    {
        return $this->log(request());
    }

    /**
     * Process absen masuk (CHECK-IN)
     * UPDATED: Validasi lokasi dari database
     */
    public function masuk(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'face_encoding' => 'required|json',
                'face_image' => 'required',
                'face_confidence' => 'required|numeric'
            ]);

            $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();
            $today = date('Y-m-d');

            // ===== GET LOKASI KANTOR DARI DATABASE =====
            $lokasiKantor = LokasiKantor::first();

            if (!$lokasiKantor) {
                return back()->with('error', 'Lokasi kantor belum dikonfigurasi!');
            }

            // STEP 1: Find atau auto-create absensi hari ini
            $absensi = Absensi::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', $today)
                ->first();

            if (!$absensi) {
                // Auto-generate record absensi jika belum ada
                $absensi = Absensi::create([
                    'karyawan_id' => $karyawan->id,
                    'tanggal' => $today,
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'status' => 'alpa',
                    'face_valid' => 0,
                    'verification_method' => 'system',
                ]);
            }

            // Check sudah check-in?
            if ($absensi->jam_masuk) {
                return redirect()->route('karyawan.dashboard')
                    ->with('error', 'Anda sudah absen masuk hari ini!');
            }

            // STEP 2: Validasi lokasi dengan data dari database
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $lokasiKantor->latitude,
                $lokasiKantor->longitude
            );

            if ($distance > $lokasiKantor->radius) {
                return back()->with('error',
                    'Anda berada di luar radius kantor! ' .
                    'Lokasi: ' . $lokasiKantor->nama_lokasi . ' | ' .
                    'Jarak: ' . round($distance) . 'm | ' .
                    'Radius: ' . $lokasiKantor->radius . 'm'
                );
            }

            // STEP 3: Validasi face
            $wajahTerdaftar = WajahKaryawan::where('karyawan_id', $karyawan->id)->first();
            if (!$wajahTerdaftar) {
                return back()->with('error', 'Wajah Anda belum terdaftar!');
            }

            $encodingBaru = json_decode($request->face_encoding);
            $encodingTerdaftar = json_decode($wajahTerdaftar->face_encoding);
            $faceDistance = $this->euclideanDistance($encodingBaru, $encodingTerdaftar);

            if ($faceDistance >= 0.6) {
                return back()->with('error', 'Wajah tidak cocok! Distance: ' . round($faceDistance, 3));
            }

            // STEP 4: Simpan foto dengan kompresi
            $fotoPath = null;
            if ($request->face_image) {
                $fotoPath = $this->saveFotoAbsensi($request->face_image, 'masuk');

                if (!$fotoPath) {
                    Log::warning('Failed to save photo, continuing without photo');
                }
            }

            // STEP 5: Get shift aktif untuk hari ini
            $shift = $this->getActiveShift($karyawan, $today);

            if (!$shift) {
                return back()->with('error', 'Shift departemen anda belum ditentukan. Hubungi admin!');
            }

            $now = Carbon::now();
            $jamMasuk = $now->format('H:i:s');

            // Jam masuk shift hari ini
            $jamMasukShift = Carbon::parse($today . ' ' . $shift->jam_masuk);

            // Toleransi keterlambatan
            $batasTerlambat = $jamMasukShift->copy()->addMinutes($shift->toleransi_menit);

            $status = $now->lte($batasTerlambat) ? 'hadir' : 'terlambat';

            // STEP 6: UPDATE absensi (BUKAN CREATE)
            $absensi->update([
                'jam_masuk' => $jamMasuk,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'foto_masuk' => $fotoPath,
                'face_valid' => 1,
                'face_confidence' => $request->face_confidence,
                'face_distance' => $faceDistance,
                'verification_method' => 'face',
                'status' => $status,
                'lokasi_kantor_id' => $lokasiKantor->id,  // Catat lokasi kantor mana yang digunakan
            ]);

            activity_log(
                'absensi',
                'clock_in',
                'Absen masuk pukul ' . now()->format('H:i')
            );

            Notifikasi::create([
                'user_id' => Auth::id(),
                'judul' => 'Absen Berhasil',
                'pesan' => 'Anda berhasil melakukan absen masuk hari ini di ' . $lokasiKantor->nama_lokasi . '.',
                'type' => 'absensi',
                'target_role' => 'karyawan'
            ]);

            $message = $status == 'hadir'
                ? '✔ Absen masuk berhasil! Anda tepat waktu di ' . $lokasiKantor->nama_lokasi . '.'
                : '⚠ Absen masuk berhasil, tetapi Anda terlambat.';

            $type = $status == 'hadir' ? 'success' : 'warning';
            $title = $status == 'hadir' ? 'Absen Berhasil!' : 'Absen Berhasil (Terlambat)';

            return redirect()->route('karyawan.dashboard')->with('alert', [
                'type' => $type,
                'title' => $title,
                'message' => $message
            ]);

        } catch (Exception $e) {
            Log::error('Absen Masuk Error: ' . $e->getMessage());

            return back()->with('alert', [
                'type' => 'error',
                'title' => 'Gagal!',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show form absen pulang
     * UPDATED: Pass lokasi kantor ke view
     */
    public function pulangForm()
    {
        $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();

        // Cek apakah sudah absen masuk hari ini
        $today = date('Y-m-d');
        $absensi = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Jika tidak ada absensi hari ini, cek kemarin (untuk shift lintas hari)
        if (!$absensi) {
            $yesterday = Carbon::yesterday()->toDateString();
            $shift = $this->getActiveShift($karyawan, $yesterday);

            if ($shift && $shift->lintas_hari) {
                $absensi = Absensi::where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $yesterday)
                    ->first();
            }
        }

        if (!$absensi || !$absensi->jam_masuk) {
            return redirect()->route('karyawan.dashboard')->with('alert', [
                'type' => 'warning',
                'title' => 'Anda Belum Absen Masuk!',
                'message' => 'Anda belum absen masuk hari ini!'
            ]);
        }

        if ($absensi->jam_pulang) {
            return redirect()->route('karyawan.dashboard')->with('alert', [
                'type' => 'warning',
                'title' => 'Sudah Absen Pulang!',
                'message' => 'Anda sudah absen pulang hari ini!'
            ]);
        }

        // Ambil data wajah karyawan
        $wajahKaryawan = WajahKaryawan::where('karyawan_id', $karyawan->id)->first();

        if (!$wajahKaryawan) {
            return redirect()->route('karyawan.dashboard')->with('alert', [
                'type' => 'error',
                'title' => 'Wajah Belum Terdaftar',
                'message' => 'Wajah Anda belum terdaftar! Lakukan registrasi terlebih dahulu.'
            ]);
        }

        // ===== TAMBAHAN: Get lokasi kantor dari database =====
        $lokasiKantor = LokasiKantor::first();

        if (!$lokasiKantor) {
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Lokasi kantor belum dikonfigurasi! Hubungi admin.');
        }

        return view('karyawan.absensi.pulang-form', compact('wajahKaryawan', 'lokasiKantor'));
    }

    /**
     * Process absen pulang (CHECK-OUT)
     * UPDATED: Validasi lokasi dari database
     */
    public function pulang(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'face_encoding' => 'required|json',
                'face_image' => 'required',
                'face_confidence' => 'required|numeric'
            ]);

            $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();
            $today = Carbon::now()->toDateString();

            // ===== GET LOKASI KANTOR DARI DATABASE =====
            $lokasiKantor = LokasiKantor::first();

            if (!$lokasiKantor) {
                return back()->with('error', 'Lokasi kantor belum dikonfigurasi!');
            }

            // STEP 1: Cari absen hari ini
            $absensi = Absensi::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', $today)
                ->first();

            // STEP 2: Jika tidak ada hari ini, cek kemarin untuk shift lintas hari
            if (!$absensi) {
                $yesterday = Carbon::yesterday()->toDateString();
                $shift = $this->getActiveShift($karyawan, $yesterday);

                if ($shift && $shift->lintas_hari) {
                    $absensi = Absensi::where('karyawan_id', $karyawan->id)
                        ->whereDate('tanggal', $yesterday)
                        ->first();
                }
            }

            if (!$absensi || !$absensi->jam_masuk) {
                return redirect()->route('karyawan.dashboard')->with('alert', [
                    'type' => 'warning',
                    'title' => 'Anda Belum Absen Masuk!',
                    'message' => 'Anda belum absen masuk hari ini!'
                ]);
            }

            if ($absensi->jam_pulang) {
                return redirect()->route('karyawan.dashboard')->with('alert', [
                    'type' => 'warning',
                    'title' => 'Sudah Absen Pulang!',
                    'message' => 'Anda sudah absen pulang hari ini!'
                ]);
            }

            // STEP 3: Validasi lokasi dengan data dari database
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $lokasiKantor->latitude,
                $lokasiKantor->longitude
            );

            if ($distance > $lokasiKantor->radius) {
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Diluar Radius!',
                    'message' => 'Anda berada diluar radius kantor ' . $lokasiKantor->nama_lokasi . '! (Jarak: ' . round($distance) . 'm)'
                ]);
            }

            // STEP 4: Validasi face
            $wajahTerdaftar = WajahKaryawan::where('karyawan_id', $karyawan->id)->first();
            if (!$wajahTerdaftar) {
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Wajah Belum Terdaftar',
                    'message' => 'Wajah Anda belum terdaftar! Lakukan registrasi terlebih dahulu.'
                ]);
            }

            $encodingBaru = json_decode($request->face_encoding);
            $encodingTerdaftar = json_decode($wajahTerdaftar->face_encoding);
            $faceDistance = $this->euclideanDistance($encodingBaru, $encodingTerdaftar);

            if ($faceDistance >= 0.6) {
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Wajah Tidak Cocok',
                    'message' => 'Wajah tidak cocok! Distance: ' . round($faceDistance, 3)
                ]);
            }

            // STEP 5: Simpan foto pulang dengan kompresi
            $fotoPath = null;
            if ($request->face_image) {
                $fotoPath = $this->saveFotoAbsensi($request->face_image, 'pulang');

                if (!$fotoPath) {
                    Log::warning('Failed to save photo, continuing without photo');
                }
            }

            // STEP 6: UPDATE jam_pulang
            $absensi->update([
                'jam_pulang' => Carbon::now()->format('H:i:s'),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'foto_pulang' => $fotoPath,
                'face_confidence' => $request->face_confidence,
                'face_distance' => $faceDistance,
                'lokasi_kantor_id' => $lokasiKantor->id,  // Catat lokasi kantor
            ]);

            // STEP 7: Check apakah absen di hari libur nasional
            if (HariLiburNasional::isHariLiburNasional($absensi->tanggal)) {
                LiburPengganti::addSaldo($karyawan->id, 1);
            }

            activity_log(
                'absensi',
                'clock_out',
                'Absen pulang pukul ' . now()->format('H:i')
            );

            Notifikasi::create([
                'user_id' => Auth::id(),
                'judul' => 'Absen Pulang Berhasil',
                'pesan' => 'Anda berhasil melakukan absen pulang. Hati-hati di jalan!',
                'type' => 'absensi',
                'target_role' => 'karyawan'
            ]);

            return redirect()->route('karyawan.dashboard')->with('alert', [
                'type' => 'success',
                'title' => 'Absen Pulang Berhasil',
                'message' => 'Absen pulang berhasil! Hati-hati di jalan.'
            ]);

        } catch (Exception $e) {
            Log::error('Absen Pulang Error: ' . $e->getMessage());
            return back()->with('alert', [
                'type' => 'error',
                'title' => 'Gagal!',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

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
            ->first();

        if ($shiftSementara) {
            return $shiftSementara->shiftBaru;
        }

        // 2. Jika tidak ada shift sementara, ambil shift dari shift pattern
        $date = \Carbon\Carbon::parse($tanggal);
        $namaHari = \App\Models\KaryawanShiftPattern::getNamaHari($date);
        $pattern = \App\Models\KaryawanShiftPattern::getPatternForDate($karyawan->id, $date);

        if ($pattern && isset($pattern[$namaHari]) && $pattern[$namaHari]->tipe === 'kerja' && $pattern[$namaHari]->shift_id) {
            return \App\Models\Shift::find($pattern[$namaHari]->shift_id);
        }

        return null;
    }

    /**
     * Simpan foto dengan kompresi aggressive
     */
    private function saveFotoAbsensi($base64Image, $type = 'masuk')
    {
        try {
            if (strpos($base64Image, ',') !== false) {
                $base64Image = explode(',', $base64Image)[1];
            }

            $imageData = base64_decode($base64Image);

            $manager = new ImageManager(new Driver());
            $image = $manager->read($imageData);

            $maxWidth = 800;
            $maxHeight = 800;
            $quality = 60;

            $image->resize($maxWidth, $maxHeight);

            $encoded = $image->toJpeg($quality);

            $karyawanId = auth()->user()->karyawan->id;
            $timestamp = now()->format('Ymd_His');
            $random = substr(md5(uniqid()), 0, 6);

            $filename = "absensi_{$karyawanId}_{$type}_{$timestamp}_{$random}.jpg";

            Storage::disk('public')->put(
                "absensi/{$filename}",
                $encoded
            );

            return "absensi/{$filename}";

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function verifyFace(Request $request)
    {
        try {
            $request->validate([
                'face_encoding' => 'required|json',
            ]);

            $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();

            $wajahTerdaftar = WajahKaryawan::where('karyawan_id', $karyawan->id)->first();

            if (!$wajahTerdaftar) {
                return response()->json([
                    'valid'   => false,
                    'message' => 'Wajah belum terdaftar.',
                ]);
            }

            $encodingBaru      = json_decode($request->face_encoding);
            $encodingTerdaftar = json_decode($wajahTerdaftar->face_encoding);
            $distance          = $this->euclideanDistance($encodingBaru, $encodingTerdaftar);

            return response()->json([
                'valid'    => $distance < 0.6,
                'distance' => round($distance, 4),
                'message'  => $distance < 0.6 ? 'Wajah cocok.' : 'Wajah tidak cocok.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'valid'   => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
        /**
     * Simpan data wajah (registrasi pertama kali)
     * POST /karyawan/wajah/store
     */
    public function registerFaceStore(Request $request)
    {
        try {
            $request->validate([
                'face_encoding'   => 'required|json',
                'face_image'      => 'required',
                'face_confidence' => 'required|numeric',
            ]);

            $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();

            if ($karyawan->wajah_terdaftar) {
                return redirect()->route('karyawan.dashboard')
                    ->with('error', 'Wajah Anda sudah terdaftar!');
            }

            // Simpan foto wajah
            $fotoPath = null;
            if ($request->face_image) {
                $base64 = $request->face_image;
                if (strpos($base64, ',') !== false) {
                    $base64 = explode(',', $base64)[1];
                }

                $manager = new ImageManager(new Driver());
                $image   = $manager->read(base64_decode($base64));
                $image->resize(400, 400);
                $encoded = $image->toJpeg(80);

                $filename = "wajah_{$karyawan->id}_" . now()->format('Ymd_His') . '.jpg';
                Storage::disk('public')->put("wajah/{$filename}", $encoded);
                $fotoPath = "wajah/{$filename}";
            }

            // Simpan encoding wajah
            WajahKaryawan::updateOrCreate(
                ['karyawan_id' => $karyawan->id],
                [
                    'face_encoding'   => $request->face_encoding,
                    'foto_wajah'      => $fotoPath,
                    'face_confidence' => $request->face_confidence,
                ]
            );

            // Tandai wajah sudah terdaftar
            $karyawan->update(['wajah_terdaftar' => true]);

            activity_log('wajah', 'register', 'Berhasil mendaftarkan wajah');

            return redirect()->route('karyawan.dashboard')
                ->with('success', '✅ Wajah berhasil didaftarkan! Anda sekarang bisa absen dengan face recognition.');

        } catch (Exception $e) {
            Log::error('Register Face Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mendaftarkan wajah: ' . $e->getMessage());
        }
    }

        public function registerFaceForm()
    {
        $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();

        if ($karyawan->wajah_terdaftar) {
            return redirect()->route('karyawan.dashboard')
                ->with('info', 'Wajah Anda sudah terdaftar.');
        }

        $wajahKaryawan = WajahKaryawan::where('karyawan_id', $karyawan->id)->first();

        return view('karyawan.wajah.register', compact('karyawan', 'wajahKaryawan'));
    }

    /**
     * Riwayat absensi & cuti karyawan
     * GET /karyawan/absensi/log
     *
     * Support filter spesifik:
     * - tanggal: 1-31 (opsional)
     * - bulan: 1-12 (default: bulan ini)
     * - tahun: 2020-skrg (default: tahun ini)
     *
     * Contoh URL:
     * /karyawan/absensi/log?bulan=3&tahun=2026 (maret 2026)
     * /karyawan/absensi/log?tanggal=15&bulan=3&tahun=2026 (15 maret 2026)
     */
    public function log(Request $request)
    {
        $karyawan = Karyawan::where('user_id', Auth::id())->firstOrFail();

        // Get filter parameters
        $tanggal = $request->get('tanggal', '');
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // Validate input
        $tanggal = is_numeric($tanggal) && $tanggal >= 1 && $tanggal <= 31 ? (int)$tanggal : null;
        $bulan = is_numeric($bulan) && $bulan >= 1 && $bulan <= 12 ? (int)$bulan : date('m');
        $tahun = is_numeric($tahun) && $tahun >= 2020 ? (int)$tahun : date('Y');

        // ===== ABSENSI QUERY =====
        $absensiQuery = Absensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        // Add tanggal filter jika ada
        if ($tanggal) {
            $absensiQuery->whereDay('tanggal', $tanggal);
        }

        $absensi = $absensiQuery->orderBy('tanggal', 'desc')
            ->paginate(20, ['*'], 'absensi_page');

        // ===== CUTI QUERY =====
        $cutiQuery = Cuti::with(['jenisCuti', 'approvals.approver'])
            ->where('karyawan_id', $karyawan->id)
            ->where(function ($q) use ($bulan, $tahun, $tanggal) {
                // Cuti yang dibuat dalam bulan/tahun yang difilter
                $q->whereMonth('created_at', $bulan)
                  ->whereYear('created_at', $tahun);
            });

        // Add tanggal filter jika ada (cek tanggal created_at)
        if ($tanggal) {
            $cutiQuery->whereDay('created_at', $tanggal);
        }

        $cuti = $cutiQuery->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'cuti_page');

        return view('karyawan.absensi.log', [
            'absensi' => $absensi,
            'cuti' => $cuti,
            'karyawan' => $karyawan,
            'tanggal' => $tanggal,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }


    /**
     * Calculate distance between two GPS coordinates (Haversine formula)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Euclidean distance for face matching
     */
    private function euclideanDistance($arr1, $arr2)
    {
        $sum = 0;
        for ($i = 0; $i < count($arr1); $i++) {
            $sum += pow($arr1[$i] - $arr2[$i], 2);
        }
        return sqrt($sum);
    }
}
