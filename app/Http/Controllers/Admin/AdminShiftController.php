<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shift;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\AjukanShift;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shift = Shift::all();
        return view('admin.shift.index', compact('shift'));
    }


        public function store(Request $request)
    {
        try {
            $request->validate([
                'kode'     => 'required|',
                'jam_masuk' => 'required|',
                'jam_pulang'     => 'required|',
                'toleransi_menit'   => 'required|',
                // 'lintas_hari' => 'required'
            ]);

            Shift::create([
                'kode'     => $request->kode,
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang'     => $request->jam_pulang,
                'toleransi_menit'   => $request->toleransi_menit,
                'lintas_hari'     => $request->has('lintas_hari'),
            ]);

            activity_log(
                'shift',
                'create',
                'Menambahkan shift ' . $request->kode
            );

            return redirect()->back()->with('success', 'Shift berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan shift!');
        }
    }

    public function update(Request $request, Shift $shift)
    {
        try {
            $request->validate([
                'kode'     => 'required|',

                'jam_masuk' => 'required|',
                'jam_pulang'     => 'required|',
                'toleransi_menit'   => 'required|',
                // 'lintas_hari' => 'required'
            ]);

            $shift->update([
                'kode'     => $request->kode,
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang'     => $request->jam_pulang,
                'toleransi_menit'   => $request->toleransi_menit,
                'lintas_hari'     => $request->has('lintas_hari'),
            ]);

            activity_log(
                'shift',
                'update',
                'Mengubah shift ' . $shift->kode
            );

            return redirect()->back()->with('success', 'Shift berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui shift!');
        }
    }

    public function destroy(Shift $shift)
    {
        try {
            $shift->delete();
            return redirect()->back()->with('success', 'Shift berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Shift gagal dihapus');
        }
    }

}
