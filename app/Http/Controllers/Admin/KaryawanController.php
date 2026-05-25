<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\JatahCuti;
use App\Models\Departemen;
use App\Helpers\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    /**
     * Constructor - Check authorization
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if user can CRUD karyawan
     * Hanya super_admin
     */
    private function canCRUD(): bool
    {
        return RoleHelper::canCrudKaryawan();
    }

    /**
     * Check if user can view karyawan
     * super_admin, admin
     */
    private function canView(): bool
    {
        return RoleHelper::canViewKaryawan();
    }

    /**
     * Get current user's department (untuk admin)
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
     * Apply department filter untuk admin
     */
    private function applyDepartmentFilter($query)
    {
        $role = Auth::user()->role;
        $departmentId = $this->getUserDepartmentId();

        // Admin hanya melihat departemennya sendiri
        if ($role === 'admin' && $departmentId) {
            $query->where('departemen_id', $departmentId);
        }

        return $query;
    }

    /**
     * Show list karyawan
     * GET /admin/karyawan
     */
    public function index()
    {
        if (!$this->canView()) {
            abort(403, 'Unauthorized access');
        }

        $query = Karyawan::with(['user', 'departemen', 'jabatan']);
        $karyawan = $this->applyDepartmentFilter($query)->get();

        $jabatan = Jabatan::all();
        $users = User::whereDoesntHave('karyawan')->get();
        $departemen = Departemen::all();

        return view('admin.karyawan.index', [
            'karyawan'          => $karyawan,
            'jabatan'           => $jabatan,
            'users'             => $users,
            'departemen'        => $departemen,
            'canCRUD'           => $this->canCRUD(),
            'currentRole'       => Auth::user()->role,
            'currentDepartment' => $this->getUserDepartmentId(),
        ]);
    }

    /**
     * Store karyawan
     * POST /admin/karyawan
     */
    public function store(Request $request)
    {
        if (!$this->canCRUD()) {
            abort(403, 'Anda tidak memiliki izin untuk menambah karyawan');
        }

        $request->validate([
            'user_id'       => 'required|exists:users,id|unique:karyawan,user_id',
            'nip'           => 'required|unique:karyawan,nip',
            'departemen_id' => 'required',
            'jabatan_id'    => 'required|exists:jabatan,id',
            'no_telepon'    => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->user_id);
            $user->update(['status' => 'aktif']);

            $karyawan = Karyawan::create([
                'user_id'       => $user->id,
                'nip'           => $request->nip,
                'departemen_id' => $request->departemen_id,
                'jabatan_id'    => $request->jabatan_id,
                'no_telepon'    => $request->no_telepon,
                'alamat'        => $request->alamat,
                'status'        => 'aktif',
            ]);

            JatahCuti::create([
                'karyawan_id' => $karyawan->id,
                'tahun'       => now()->year,
                'jatah_awal'  => 0,
                'jatah'       => 0
            ]);

            activity_log('karyawan', 'create', 'Menambahkan karyawan ' . $karyawan->user->nama);

            DB::commit();
            return redirect()->route('admin.karyawan.index')
                ->with('success', 'Karyawan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menambah karyawan: ' . $e->getMessage()]);
        }
    }

    /**
     * Show detail karyawan
     * GET /admin/karyawan/{id}
     */
    public function show($id)
    {
        if (!$this->canView()) {
            abort(403, 'Unauthorized access');
        }

        $query = Karyawan::with(['user', 'departemen', 'jabatan', 'jatahCuti' => function ($q) {
            $q->where('tahun', date('Y'));
        }]);

        $query = $this->applyDepartmentFilter($query);
        $karyawan = $query->findOrFail($id);

        $wajahKaryawan = \App\Models\WajahKaryawan::where('karyawan_id', $karyawan->id)->first();

        return view('admin.karyawan.show', [
            'karyawan'      => $karyawan,
            'wajahKaryawan' => $wajahKaryawan,
            'canCRUD'       => $this->canCRUD(),
        ]);
    }

    /**
     * Update karyawan
     * PUT /admin/karyawan/{id}
     */
    public function update(Request $request, $id)
    {
        if (!$this->canCRUD()) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah data karyawan');
        }

        $karyawan = Karyawan::with('user')->findOrFail($id);

        $request->validate([
            'nip'           => 'required|unique:karyawan,nip,' . $id,
            'departemen_id' => 'required',
            'jabatan_id'    => 'required|exists:jabatan,id',
            'status'        => 'required|in:aktif,nonaktif',
            'no_telepon'    => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $karyawan->update([
                'nip'           => $request->nip,
                'departemen_id' => $request->departemen_id,
                'jabatan_id'    => $request->jabatan_id,
                'status'        => $request->status,
                'no_telepon'    => $request->no_telepon,
                'alamat'        => $request->alamat,
            ]);

            $karyawan->user->update(['status' => $request->status]);

            activity_log('karyawan', 'update', 'Mengubah data karyawan ' . $karyawan->user->nama);

            DB::commit();
            return redirect()->route('admin.karyawan.index')
                ->with('success', 'Data karyawan berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal update karyawan: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete karyawan
     * DELETE /admin/karyawan/{id}
     */
    public function destroy($id)
    {
        if (!$this->canCRUD()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus karyawan');
        }

        $karyawan = Karyawan::with('user')->findOrFail($id);

        DB::beginTransaction();
        try {
            $namaKaryawan = $karyawan->user->nama;
            $karyawan->delete();

            activity_log('karyawan', 'delete', 'Menghapus profil karyawan ' . $namaKaryawan);

            DB::commit();
            return redirect()->route('admin.karyawan.index')
                ->with('success', 'Karyawan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus karyawan: ' . $e->getMessage()]);
        }
    }
    public function updateSection(Request $request, $id)
    {
        if (!$this->canCRUD()) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah data karyawan');
        }

        $karyawan = Karyawan::findOrFail($id);
        $section = $request->input('section');

        try {
            if ($section === 'personal') {
                $request->validate([
                    'no_telepon'          => 'nullable|string|max:20',
                    'no_telepon_tambahan' => 'nullable|string|max:20',
                    'tempat_lahir'        => 'nullable|string|max:100',
                    'tanggal_lahir'       => 'nullable|date',
                    'jenis_kelamin'       => 'nullable|in:laki-laki,perempuan',
                    'status_pernikahan'   => 'nullable|in:belum_menikah,menikah,cerai',
                    'golongan_darah'      => 'nullable|string|max:5',
                    'agama'               => 'nullable|string|max:20',
                ]);

                $karyawan->update($request->only([
                    'no_telepon', 'no_telepon_tambahan', 'tempat_lahir', 'tanggal_lahir',
                    'jenis_kelamin', 'status_pernikahan', 'golongan_darah', 'agama'
                ]));
            } elseif ($section === 'identity') {
                $request->validate([
                    'nik'                 => 'nullable|string|max:16',
                    'alamat_ktp'          => 'nullable|string|max:500',
                    'kode_pos'            => 'nullable|string|max:10',
                    'alamat_tinggal'      => 'nullable|string|max:500',
                    'no_paspor'           => 'nullable|string|max:30',
                    'masa_berlaku_paspor' => 'nullable|date',
                ]);

                $karyawan->update($request->only([
                    'nik', 'alamat_ktp', 'kode_pos', 'alamat_tinggal',
                    'no_paspor', 'masa_berlaku_paspor'
                ]));
            }

            activity_log('karyawan', 'update', 'Mengubah section ' . $section . ' karyawan ' . $karyawan->user->nama);

            return back()->with('success', 'Data section ' . $section . ' berhasil diupdate');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update section: ' . $e->getMessage()]);
        }
    }
}