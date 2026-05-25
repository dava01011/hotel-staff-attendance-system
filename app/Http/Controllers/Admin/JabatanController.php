<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatan = Jabatan::withCount('karyawan')->paginate(20);
        return view('admin.jabatan.index', compact('jabatan'));
    }

    public function create()
    {
        return view('admin.jabatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'jatah_cuti_bulanan' => 'required|numeric|min:0',
            'tipe_gaji' => 'required|in:harian,bulanan',
            'gaji_pokok' => 'required_if:tipe_gaji,bulanan|nullable|numeric|min:0',
            'gaji_harian' => 'required_if:tipe_gaji,harian|nullable|numeric|min:0',
        ]);

        Jabatan::create($request->all());

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        return view('admin.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);

        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'jatah_cuti_bulanan' => 'required|numeric|min:0',
            'tipe_gaji' => 'required|in:harian,bulanan',
            'gaji_pokok' => 'required_if:tipe_gaji,bulanan|nullable|numeric|min:0',
            'gaji_harian' => 'required_if:tipe_gaji,harian|nullable|numeric|min:0',
        ]);

        $jabatan->update($request->all());

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil diupdate');
    }

    public function destroy($id)
    {
        $jabatan = Jabatan::findOrFail($id);

        if ($jabatan->karyawan()->count() > 0) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus jabatan yang masih memiliki karyawan']);
        }

        $jabatan->delete();

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus');
    }
}
