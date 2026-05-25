<?php

namespace App\Http\Controllers\Admin;

use App\Models\JenisCuti;
use App\Helpers\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class JenisCutiController extends Controller
{
    /**
     * Cek apakah user bisa CRUD (Super Admin / HRD)
     */
    private function canCRUD(): bool
    {
        return RoleHelper::canCrudCuti();
    }

    /**
     * Cek apakah user bisa melihat halaman
     */
    private function canView(): bool
    {
        return RoleHelper::canViewCuti();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!$this->canView()) {
            abort(403, 'Unauthorized access.');
        }

        $jenisCuti = JenisCuti::orderBy('aktif', 'desc')
            ->orderBy('nama')
            ->get();

        return view('admin.jenis_cuti.index', [
            'jenisCuti' => $jenisCuti,
            'canCRUD'   => $this->canCRUD(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->canCRUD()) {
            abort(403, 'Anda tidak memiliki izin untuk menambah data.');
        }

        $validated = $request->validate([
            'nama'         => 'required|string|max:100|unique:jenis_cuti,nama',
            'deskripsi'    => 'nullable|string',
            'butuh_file'   => 'nullable|in:0,1',
            'potong_jatah' => 'nullable|in:0,1',
            'aktif'        => 'nullable|in:0,1',
        ]);

        JenisCuti::create([
            'nama'         => $validated['nama'],
            'deskripsi'    => $validated['deskripsi'] ?? null,
            'butuh_file'   => (bool) ($request->input('butuh_file', 0)),
            'potong_jatah' => (bool) ($request->input('potong_jatah', 1)),
            'aktif'        => (bool) ($request->input('aktif', 1)),
        ]);

        return redirect()
            ->route('admin.jenis-cuti.index')
            ->with('success', "✅ Jenis cuti '{$validated['nama']}' berhasil ditambahkan!");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisCuti $jenisCuti)
    {
        if (!$this->canCRUD()) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah data.');
        }

        $validated = $request->validate([
            'nama'      => 'required|string|max:100|unique:jenis_cuti,nama,' . $jenisCuti->id,
            'deskripsi' => 'nullable|string',
        ]);

        $jenisCuti->update([
            'nama'         => $validated['nama'],
            'deskripsi'    => $validated['deskripsi'] ?? null,
            'butuh_file'   => $request->input('butuh_file')   === '1',
            'potong_jatah' => $request->input('potong_jatah') === '1',
            'aktif'        => $request->input('aktif')        === '1',
        ]);

        return redirect()
            ->route('admin.jenis-cuti.index')
            ->with('success', "✅ Jenis cuti '{$jenisCuti->nama}' berhasil diupdate!");
    }

    /**
     * Toggle status aktif/nonaktif.
     */
    public function toggle(JenisCuti $jenisCuti)
    {
        if (!$this->canCRUD()) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah status.');
        }

        $jenisCuti->update(['aktif' => !$jenisCuti->aktif]);

        $status = $jenisCuti->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.jenis-cuti.index')
            ->with('success', "✅ Jenis cuti '{$jenisCuti->nama}' berhasil {$status}!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisCuti $jenisCuti)
    {
        if (!$this->canCRUD()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus data.');
        }

        $nama = $jenisCuti->nama;
        $jenisCuti->delete();

        return redirect()
            ->route('admin.jenis-cuti.index')
            ->with('success', "✅ Jenis cuti '{$nama}' berhasil dihapus!");
    }
}