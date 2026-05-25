<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Pengumuman::with(['pembuat', 'departemen'])->latest();

        // Jika admin departemen (manager), hanya lihat pengumuman departemennya
        if ($user->role === 'admin' && $user->karyawan) {
            $query->where('tipe', 'departemen')
                  ->where('departemen_id', $user->karyawan->departemen_id);
        }

        $pengumuman = $query->get();
        $departemen = Departemen::all();

        return view('admin.pengumuman.index', compact('pengumuman', 'departemen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
        ];

        // Jika super_admin atau hrd, bisa memilih tipe
        if (in_array($user->role, ['super_admin', 'gm'])) {
            $rules['tipe'] = 'required|in:global,departemen';
            $rules['departemen_id'] = 'required_if:tipe,departemen';
        }

        $validated = $request->validate($rules);

        $pengumuman = new Pengumuman();
        $pengumuman->pembuat_id = $user->id;
        $pengumuman->judul = $validated['judul'];
        $pengumuman->konten = $validated['konten'];

        if ($user->role === 'admin' && $user->karyawan) {
            // Force ke departemen sendiri
            $pengumuman->tipe = 'departemen';
            $pengumuman->departemen_id = $user->karyawan->departemen_id;
        } else {
            $pengumuman->tipe = $validated['tipe'];
            $pengumuman->departemen_id = $validated['tipe'] === 'departemen' ? $validated['departemen_id'] : null;
        }

        $pengumuman->save();

        activity_log('pengumuman', 'create', 'Membuat pengumuman: ' . $pengumuman->judul);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $pengumuman = Pengumuman::findOrFail($id);

        // Authorization check
        if ($user->role === 'admin' && $user->karyawan) {
            if ($pengumuman->departemen_id != $user->karyawan->departemen_id) {
                abort(403, 'Unauthorized access');
            }
        }

        $rules = [
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
        ];

        if (in_array($user->role, ['super_admin', 'gm'])) {
            $rules['tipe'] = 'required|in:global,departemen';
            $rules['departemen_id'] = 'required_if:tipe,departemen';
        }

        $validated = $request->validate($rules);

        $pengumuman->judul = $validated['judul'];
        $pengumuman->konten = $validated['konten'];

        if (in_array($user->role, ['super_admin', 'gm'])) {
            $pengumuman->tipe = $validated['tipe'];
            $pengumuman->departemen_id = $validated['tipe'] === 'departemen' ? $validated['departemen_id'] : null;
        }

        $pengumuman->save();

        activity_log('pengumuman', 'update', 'Memperbarui pengumuman: ' . $pengumuman->judul);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $pengumuman = Pengumuman::findOrFail($id);

        // Authorization check
        if ($user->role === 'admin' && $user->karyawan) {
            if ($pengumuman->departemen_id != $user->karyawan->departemen_id) {
                abort(403, 'Unauthorized access');
            }
        }

        $judul = $pengumuman->judul;
        $pengumuman->delete();

        activity_log('pengumuman', 'delete', 'Menghapus pengumuman: ' . $judul);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
