<?php

namespace App\Http\Controllers\Admin;

use App\Models\Karyawan;
use App\Models\JatahCuti;
use App\Helpers\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class JatahCutiController extends Controller
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
     * Ambil ID departemen user yang sedang login
     */
    private function getUserDepartmentId()
    {
        $user = Auth::user();
        if ($user && $user->karyawan) {
            return $user->karyawan->departemen_id;
        }
        return null;
    }

    /**
     * Filter data berdasarkan departemen untuk admin
     */
    private function applyDepartmentFilter($query)
    {
        $user = Auth::user();
        $role = $user->role;
        $deptId = $this->getUserDepartmentId();

        if ($role === 'admin' && $deptId) {
            $query->whereHas('karyawan', function ($q) use ($deptId) {
                $q->where('departemen_id', $deptId);
            });
        }

        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!$this->canView()) {
            abort(403, 'Unauthorized access.');
        }

        $query = JatahCuti::with(['karyawan.user', 'karyawan.departemen'])
            ->orderByDesc('tahun')
            ->orderBy('karyawan_id');

        $jatahCuti = $this->applyDepartmentFilter($query)->get();

        // Data karyawan untuk dropdown (hanya untuk yang bisa CRUD)
        $karyawan = [];
        if ($this->canCRUD()) {
            $karyawan = Karyawan::with('user')
                ->where('status', 'aktif')
                ->orderBy('id')
                ->get();
        } elseif (Auth::user()->role === 'admin') {
            $deptId = $this->getUserDepartmentId();
            if ($deptId) {
                $karyawan = Karyawan::with('user')
                    ->where('departemen_id', $deptId)
                    ->where('status', 'aktif')
                    ->orderBy('id')
                    ->get();
            }
        }

        return view('admin.jatah-cuti.index', [
            'jatahCuti'        => $jatahCuti,
            'karyawan' => $karyawan,
            'canCRUD'          => $this->canCRUD(),
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

        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tahun'       => 'required|integer|min:2000|max:2100',
            'jatah_awal'  => 'required|numeric|min:0',
            'jatah'       => 'required|numeric|min:0',
        ]);

        $exists = JatahCuti::where('karyawan_id', $request->karyawan_id)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Jatah cuti untuk karyawan dan tahun tersebut sudah ada.');
        }

        JatahCuti::create([
            'karyawan_id' => $request->karyawan_id,
            'tahun'       => $request->tahun,
            'jatah_awal'  => $request->jatah_awal,
            'jatah'       => $request->jatah,
        ]);

        return redirect()->back()->with('success', 'Jatah cuti berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (!$this->canCRUD()) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah data.');
        }

        $jatahCuti = JatahCuti::findOrFail($id);

        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tahun'       => 'required|integer|min:2000|max:2100',
            'jatah_awal'  => 'required|numeric|min:0',
            'jatah'       => 'required|numeric|min:0',
        ]);

        $jatahCuti->update([
            'karyawan_id' => $request->karyawan_id,
            'tahun'       => $request->tahun,
            'jatah_awal'  => $request->jatah_awal,
            'jatah'       => $request->jatah,
        ]);

        return redirect()->back()->with('success', 'Jatah cuti berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (!$this->canCRUD()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus data.');
        }

        $jatahCuti = JatahCuti::findOrFail($id);
        $jatahCuti->delete();

        return redirect()->back()->with('success', 'Jatah cuti berhasil dihapus.');
    }
}