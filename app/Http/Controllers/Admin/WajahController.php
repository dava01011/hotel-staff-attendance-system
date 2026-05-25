<?php

namespace App\Http\Controllers\Admin;

use App\Models\Karyawan;
use App\Models\WajahKaryawan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WajahController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::with('user')
            ->where('wajah_terdaftar', 0)
            ->get();

        return view('admin.wajah.index', compact('karyawan'));
    }

    public function capture($id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);
        return view('admin.wajah.capture', compact('karyawan'));
    }

public function store(Request $request, $id)
{
    $request->validate([
        'face_encoding' => 'required|json',
        'face_image' => 'nullable'
    ]);

    $karyawan = Karyawan::findOrFail($id);

    WajahKaryawan::updateOrCreate(
        ['karyawan_id' => $id],
        [
            'face_encoding' => $request->face_encoding,
            'face_image' => $request->face_image,
            'registered_at' => now(),
            'registered_by' => auth()->id()
        ]
    );

    $karyawan->update(['wajah_terdaftar' => 1]);

    return redirect()
        ->route('admin.wajah.index')
        ->with('success', "Wajah {$karyawan->nama} berhasil didaftarkan!");
}
}
