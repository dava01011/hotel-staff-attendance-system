<?php

use App\Http\Controllers\AbsensiDetectionController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensi;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminCutiController;
use App\Http\Controllers\Admin\AdminShiftController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DepartemenController;
use App\Http\Controllers\Admin\GajiController;
use App\Http\Controllers\Admin\HariLiburNasionalController;
use App\Http\Controllers\Admin\HariLiburTemplateController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\JadwalShiftController;
use App\Http\Controllers\Admin\JatahCutiController;
use App\Http\Controllers\Admin\JenisCutiController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\LokasiKantorController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\ShiftPatternController;
use App\Http\Controllers\Admin\WajahController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\Karyawan\AbsensiController;
use App\Http\Controllers\Karyawan\AjukanShiftController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboard;
use App\Http\Controllers\Karyawan\GajiController as KaryawanGajiController;
use App\Http\Controllers\Karyawan\PengajuanController as PengajuanKaryawan;
use App\Http\Controllers\Karyawan\SettingsController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\LiburPenggantiApprovalController;
use App\Models\AjukanShift;
use Illuminate\Support\Facades\Route;

// ========================
// ROOT REDIRECT
// ========================
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        $hasAdminRole = in_array($user->role, ['admin', 'super_admin']);
        $hasKaryawanData = $user->karyawan && $user->karyawan->status === 'aktif';

        // Jika punya dual access dan belum pilih role, ke halaman pilihan
        if ($hasAdminRole && $hasKaryawanData && !session()->has('active_role')) {
            return redirect()->route('role.select');
        }

        // Redirect berdasarkan active_role atau role default
        $activeRole = session()->get('active_role');

        if ($activeRole === 'karyawan' || (!$activeRole && $hasKaryawanData && !$hasAdminRole)) {
            return redirect()->route('karyawan.dashboard');
        }

        if ($activeRole === 'admin' || (!$activeRole && $hasAdminRole)) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('karyawan.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// ========================
// GUEST ROUTES (Login & Register)
// ========================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

// ========================
// HOME REDIRECT (AUTHENTICATED)
// ========================
Route::get('/home', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();
    $hasAdminRole = in_array($user->role, ['admin', 'super_admin']);
    $hasKaryawanData = $user->karyawan && $user->karyawan->status === 'aktif';

    // Jika punya dual access dan belum pilih role, ke halaman pilihan
    if ($hasAdminRole && $hasKaryawanData && !session()->has('active_role')) {
        return redirect()->route('role.select');
    }

    $activeRole = session()->get('active_role');

    if ($activeRole === 'karyawan' || (!$activeRole && $hasKaryawanData && !$hasAdminRole)) {
        return redirect()->route('karyawan.dashboard');
    }

    if ($activeRole === 'admin' || (!$activeRole && $hasAdminRole)) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('login');
})->middleware('auth')->name('home');

// ========================
// AUTHENTICATION & ROLE SELECTION
// ========================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Role Selection (untuk dual access users)
    Route::get('/role/select', [AuthController::class, 'showRoleSelection'])->name('role.select');
    Route::post('/role/set', [AuthController::class, 'setRole'])->name('role.set');
    Route::post('/role/switch', [AuthController::class, 'switchRole'])->name('role.switch');
});


// ========================
// ADMIN ROUTES (Super Admin, Admin)
// ========================
Route::middleware(['auth', 'role:super_admin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        Route::get('/absensi/detect-form', [AbsensiDetectionController::class, 'showForm'])
        ->name('absensi.detect-form');

    Route::post('/absensi/detect-absent', [AbsensiDetectionController::class, 'detectAbsent'])
        ->name('absensi.detect-absent');

    Route::get('/api/absensi/detection-result', [AbsensiDetectionController::class, 'getDetectionResult']);

    Route::get('/absensi/test-detect', [AbsensiDetectionController::class, 'testDetect'])
        ->name('absensi.test-detect');

        Route::middleware('role:super_admin')->group(function () {
        Route::resource('lokasi-kantor', LokasiKantorController::class)->names('lokasi-kantor');
        Route::get('lokasi-kantor/{lokasiKantor}/coordinates', [LokasiKantorController::class, 'getCoordinates'])
             ->name('lokasi-kantor.coordinates');
    });

        Route::prefix('jenis-cuti')->name('jenis-cuti.')->group(function () {

    // GET  /admin/jenis-cuti
    Route::get('/', [JenisCutiController::class, 'index'])
        ->name('index');

    // POST /admin/jenis-cuti
    Route::post('/', [JenisCutiController::class, 'store'])
        ->name('store');

    // PUT  /admin/jenis-cuti/{id}
    Route::put('/{jenisCuti}', [JenisCutiController::class, 'update'])
        ->name('update');

    // POST /admin/jenis-cuti/{id}/toggle
    Route::post('/{jenisCuti}/toggle', [JenisCutiController::class, 'toggle'])
        ->name('toggle');

    // DELETE /admin/jenis-cuti/{id}
    Route::delete('/{jenisCuti}', [JenisCutiController::class, 'destroy'])
        ->name('destroy');

});
    //         Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    // Route::post('/settings/update-photo', [SettingsController::class, 'updatePhoto'])->name('settings.update-photo');
    // Route::post('/settings/update-email', [SettingsController::class, 'updateEmail'])->name('settings.update-email');
    // Route::post('/settings/update-password', [SettingsController::class, 'updatePassword'])->name('settings.update-password');

        // ===== SHIFT PATTERN MANAGEMENT (DEFAULT & WEEKLY) =====

    Route::prefix('shift-pattern')->name('shift-pattern.')->group(function () {
        // List semua karyawan & pattern mereka
        Route::get('/', [ShiftPatternController::class, 'index'])
            ->name('index');

        // ===== DEFAULT PATTERN (PERMANENT) =====

        // Form edit default pattern
        Route::get('/{karyawanId}/default/edit', [ShiftPatternController::class, 'editDefaultForm'])
            ->name('default.edit');

        // Update default pattern
        Route::post('/{karyawanId}/default/update', [ShiftPatternController::class, 'updateDefault'])
            ->name('default.update');

        // ===== WEEKLY OVERRIDE PATTERN =====

        // Form edit weekly pattern
        Route::get('/{karyawanId}/weekly/edit', [ShiftPatternController::class, 'editWeeklyForm'])
            ->name('weekly.edit');

        // Update weekly pattern
        Route::post('/{karyawanId}/weekly/update', [ShiftPatternController::class, 'updateWeekly'])
            ->name('weekly.update');

        // Delete weekly pattern
        Route::post('/{karyawanId}/weekly/delete', [ShiftPatternController::class, 'deleteWeekly'])
            ->name('weekly.delete');

        // Calendar view
        Route::get('/{karyawanId}/calendar', [ShiftPatternController::class, 'calendar'])
            ->name('calendar');
    });

    // ===== API ENDPOINTS =====

    Route::prefix('api/shift-pattern')->group(function () {
        // Get pattern untuk tanggal tertentu
        Route::get('/{karyawanId}', [ShiftPatternController::class, 'getPatternForDate'])
            ->name('api.pattern-for-date');
    });

     // ===== HARI LIBUR TEMPLATE (HYBRID: FIXED & DYNAMIC) =====

    // Route::resource('hari-libur-template', HariLiburTemplateController::class);

    // Generate hari libur untuk tahun tertentu
    // Route::get('/hari-libur-template/generate-form', [HariLiburTemplateController::class, 'generateForm'])
    //     ->name('hari-libur-template.generate-form');

    // Route::post('/hari-libur-template/generate-bulk', [HariLiburTemplateController::class, 'generateBulk'])
    //     ->name('hari-libur-template.generate-bulk');

    // Route::post('/hari-libur-template/{tahun}/generate', [HariLiburTemplateController::class, 'generate'])
    //     ->name('hari-libur-template.generate');

    // ===== HARI LIBUR NASIONAL (GENERATED FROM TEMPLATE) =====

   // ============================================================
// HARI LIBUR NASIONAL
// ============================================================

Route::prefix('hari-libur-nasional')->name('hari-libur-nasional.')->group(function () {

    // GET  /admin/hari-libur-nasional
    Route::get('/', [HariLiburNasionalController::class, 'index'])
        ->name('index');

    // GET  /admin/hari-libur-nasional/create
    Route::get('/create', [HariLiburNasionalController::class, 'create'])
        ->name('create');

    // POST /admin/hari-libur-nasional/generate/{tahun}
    // Harus sebelum /{hariLiburNasional} agar tidak tertangkap model binding
    Route::post('/generate/{tahun}', [HariLiburNasionalController::class, 'generateFixedForYear'])
        ->name('generate');

    // POST /admin/hari-libur-nasional/sync
    Route::post('/sync', [HariLiburNasionalController::class, 'syncHolidays'])
        ->name('sync');

    // POST /admin/hari-libur-nasional
    Route::post('/', [HariLiburNasionalController::class, 'store'])
        ->name('store');

    // GET  /admin/hari-libur-nasional/{id}/edit
    Route::get('/{hariLiburNasional}/edit', [HariLiburNasionalController::class, 'edit'])
        ->name('edit');

    // PUT  /admin/hari-libur-nasional/{id}
    Route::put('/{hariLiburNasional}', [HariLiburNasionalController::class, 'update'])
        ->name('update');

    // DELETE /admin/hari-libur-nasional/{id}
    Route::delete('/{hariLiburNasional}', [HariLiburNasionalController::class, 'destroy'])
        ->name('destroy');

});

// ============================================================
// LIBUR PENGGANTI
// ============================================================

Route::prefix('libur-pengganti')->name('libur-pengganti.')->group(function () {

    // GET  /admin/libur-pengganti
    Route::get('/', [HariLiburNasionalController::class, 'indexLiburPengganti'])
        ->name('index');

    // POST /admin/libur-pengganti/reset-all
    // Harus sebelum /{karyawanId} agar tidak tertangkap sebagai ID
    Route::post('/reset-all', [HariLiburNasionalController::class, 'resetAllSaldo'])
        ->name('reset-all');

    // POST /admin/libur-pengganti/{karyawanId}/adjust
    Route::post('/{karyawanId}/adjust', [HariLiburNasionalController::class, 'adjustSaldo'])
        ->name('adjust');

});

// ============================================================
// API
// ============================================================

Route::prefix('api')->group(function () {

    // GET /admin/api/libur-pengganti/{karyawanId}
    Route::get('/libur-pengganti/{karyawanId}', [HariLiburNasionalController::class, 'getSaldo'])
        ->name('api.libur-pengganti-saldo');

});

    // ========================
    // APPROVAL (Semua Role Admin, dibatasi di controller)
    // ========================
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval');
    Route::get('/approval/cuti/{id}', [ApprovalController::class, 'detail'])->name('approval-cuti.detail');
    Route::get('/cuti/{id}/detail', [ApprovalController::class, 'detailCuti'])->name('approval.cuti.detail');

    // Cuti Approval Actions
    Route::post('/cuti/{id}/approve', [ApprovalController::class, 'approveCuti'])->name('cuti.approve');
    Route::post('/cuti/{id}/reject', [ApprovalController::class, 'rejectCuti'])->name('cuti.reject');

    // Karyawan Approval (hanya super_admin)
    Route::post('/karyawan/{id}/approve', [ApprovalController::class, 'approveKaryawan'])->name('karyawan.approve');
    Route::post('/karyawan/{id}/reject', [ApprovalController::class, 'rejectKaryawan'])->name('karyawan.reject');

    // // Cuti Approval (super_admin, manager, gm, hrd - validasi di controller)
    // Route::post('/cuti/{id}/approve', [ApprovalController::class, 'approveCuti'])->name('cuti.approve');
    // Route::post('/cuti/{id}/reject', [ApprovalController::class, 'rejectCuti'])->name('cuti.reject');

    // Shift Approval (super_admin, admin)
    Route::post('/shift/{id}/approve', [ApprovalController::class, 'approveShift'])->name('shift.approve');
    Route::post('/shift/{id}/reject', [ApprovalController::class, 'rejectShift'])->name('shift.reject');

    // [TAMBAHAN] Shift detail - ada di routes 1, tidak ada di routes 2
    Route::get('/shift/{id}/detail', [ApprovalController::class, 'detailShift'])->name('approval.shift.detail');
    
    Route::get('libur-pengganti/{id}/detail', [LiburPenggantiApprovalController::class, 'detail'])->name('libur-pengganti.detail');

    // ✅ Libur Pengganti Approval (SAME CONTROLLER)
    // Route::post('libur-pengganti/{id}/approve', [ApprovalController::class, 'approveLibur'])->name('approval.libur.approve');
    // Route::post('libur-pengganti/{id}/reject', [ApprovalController::class, 'rejectLibur'])->name('approval.libur.reject');
    // Route::get('approval/libur-pengganti/{id}', [ApprovalController::class, 'detailLibur'])->name('approval.libur.detail');
    
    //approval extra off
    Route::get('libur-pengganti/approval', [LiburPenggantiApprovalController::class, 'index'])->name('libur-pengganti.approval');
    Route::post('libur-pengganti/{id}/approve', [LiburPenggantiApprovalController::class, 'approve'])->name('libur-pengganti.approve');
    Route::post('libur-pengganti/{id}/reject', [LiburPenggantiApprovalController::class, 'reject'])->name('libur-pengganti.reject');
    
    // ========================
    // MANAGEMENT FEATURES (dibatasi di view/controller berdasarkan role)
    // ========================

    // Manajemen Jabatan (super_admin, admin)
    Route::resource('jabatan', JabatanController::class);

    // Karyawan Update Section
    Route::put('/karyawan/{id}/update-section', [KaryawanController::class, 'updateSection'])->name('karyawan.update-section');
    // Manajemen Karyawan (super_admin, admin)
    Route::resource('karyawan', KaryawanController::class);

    // Pengumuman (super_admin, admin, hrd, gm)
    Route::resource('pengumuman', PengumumanController::class)->except(['create', 'show', 'edit']);

    // Laporan Absensi (super_admin, admin, manager, gm, hrd)
    // ⚠️ PENTING: Route statis HARUS sebelum route {id} agar tidak tertangkap sebagai parameter
    Route::get('absensi/export-excel',  [AdminAbsensi::class, 'exportExcel'])->name('absensi.export-excel');
    Route::get('absensi/export-pdf',    [AdminAbsensi::class, 'exportPdf'])->name('absensi.export-pdf');
    Route::get('absensi/preview-pdf',   [AdminAbsensi::class, 'previewPdf'])->name('absensi.preview-pdf');
    Route::get('absensi/preview-data',  [AdminAbsensi::class, 'previewData'])->name('absensi.preview-data');

    Route::get('/absensi', [AdminAbsensi::class, 'index'])->name('absensi.index');
    Route::post('/absensi', [AdminAbsensi::class, 'store'])->name('absensi.store');
    Route::put('/absensi/{id}', [AdminAbsensi::class, 'update'])->name('absensi.update');
    Route::delete('/absensi/{id}', [AdminAbsensi::class, 'destroy'])->name('absensi.destroy');

    // Gaji (super_admin, admin, hrd)
    Route::get('/gaji', [GajiController::class, 'index'])->name('gaji.index');
    Route::get('/gaji/create', [GajiController::class, 'create'])->name('gaji.create');
    Route::post('/gaji/hitung', [GajiController::class, 'hitung'])->name('gaji.hitung');
    Route::get('/gaji/{id}/slip', [GajiController::class, 'slip'])->name('gaji.slip');
    Route::get('/gaji/{id}/slip/pdf', [GajiController::class, 'slipPdf'])->name('gaji.slip.pdf');
    Route::delete('/gaji/{id}', [GajiController::class, 'destroy'])->name('gaji.destroy');

    // User Management (super_admin)
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    // Cuti Management (super_admin, admin, hrd)
    Route::get('/cuti', [AdminCutiController::class, 'index'])->name('cuti');

    // Face Recognition (super_admin, admin, hrd)
    Route::get('/wajah', [WajahController::class, 'index'])->name('wajah.index');
    Route::get('/wajah/capture/{id}', [WajahController::class, 'capture'])->name('wajah.capture');
    Route::post('/wajah/store/{id}', [WajahController::class, 'store'])->name('wajah.store');



    // Shift Management (super_admin, admin)
    Route::get('/shift', [AdminShiftController::class, 'index'])->name('shift.index');
    Route::post('/shift', [AdminShiftController::class, 'store'])->name('shift.store');
    Route::put('/shift/{shift}', [AdminShiftController::class, 'update'])->name('shift.update');
    Route::delete('/shift/{shift}', [AdminShiftController::class, 'destroy'])->name('shift.destroy');

    // Departemen manag (super_admin, admin)
    Route::get('/departemen', [DepartemenController::class, 'index'])->name('departemen.index');
    Route::post('/departemen', [DepartemenController::class, 'store'])->name('departemen.store');
    Route::put('/departemen/{id}', [DepartemenController::class, 'update'])->name('departemen.update');
    Route::delete('/departemen/{id}', [DepartemenController::class, 'destroy'])->name('departemen.destroy');

    // Jatah Cuti (super_admin, admin, hrd)
    Route::get('/jatah', [JatahCutiController::class, 'index'])->name('jatah-cuti.index');
    Route::post('/jatah', [JatahCutiController::class, 'store'])->name('jatah-cuti.store');
    Route::put('/jatah/{id}', [JatahCutiController::class, 'update'])->name('jatah-cuti.update');
    Route::delete('/jatah/{id}', [JatahCutiController::class, 'destroy'])->name('jatah-cuti.destroy');

    // Activity Log (super_admin, admin)
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    // Route::delete('/activity-log', [ActivityLogController::class, 'destroy'])->name('activity-log.destroy');
    Route::prefix('activity-log')->name('activity-log.')->group(function () {
    // Display logs
    Route::get('/', [ActivityLogController::class, 'index'])
        ->name('index');
 
    // Get delete stats (for confirmation)
    Route::get('/api/delete-stats', [ActivityLogController::class, 'getDeleteStats'])
        ->name('api.delete-stats');
 
    // Delete by specific date
    Route::post('/delete-by-date', [ActivityLogController::class, 'deleteByDate'])
        ->name('delete-by-date');
 
    // Delete by month & year
    Route::post('/delete-by-month', [ActivityLogController::class, 'deleteByMonth'])
        ->name('delete-by-month');
 
    // Delete by year
    Route::post('/delete-by-year', [ActivityLogController::class, 'deleteByYear'])
        ->name('delete-by-year');
 
    // Delete by date range
    Route::post('/delete-by-range', [ActivityLogController::class, 'deleteByRange'])
        ->name('delete-by-range');
 
    // Delete logs older than X days
    Route::post('/delete-older-than', [ActivityLogController::class, 'deleteOlderThan'])
        ->name('delete-older-than');
 
    // Delete by module
    Route::post('/delete-by-module', [ActivityLogController::class, 'deleteByModule'])
        ->name('delete-by-module');
 
    // Delete by action type
    Route::post('/delete-by-action', [ActivityLogController::class, 'deleteByAction'])
        ->name('delete-by-action');
 
    // Delete all logs (dangerous!)
    Route::post('/delete-all', [ActivityLogController::class, 'deleteAll'])
        ->name('delete-all');
 
    // Clear old logs (older than 90 days)
    Route::post('/clear-old', [ActivityLogController::class, 'clearOldLogs'])
        ->name('clear-old');
});

    // Ajukan Shift Departemen (admin)
    Route::get('/shift/ajukan', [AjukanShift::class, 'index'])->name('ajukan-shift.index');
    Route::post('/shift/ajukan/store', [AjukanShift::class, 'store'])->name('ajulan-shift.store');
    Route::get('/shift/ajukan/cancel/{id}', [AjukanShift::class, 'cancel'])->name('ajulan-shift.cancel');
    Route::get('/shift/ajukan/show/{id}', [AjukanShift::class, 'show'])->name('ajulan-shift.show');

    Route::get('/wajah/requests', [\App\Http\Controllers\Admin\WajahRequestAdminController::class, 'index'])
    ->name('wajah.requests');

Route::post('/wajah/requests/{id}/approve', [\App\Http\Controllers\Admin\WajahRequestAdminController::class, 'approve'])
    ->name('wajah.requests.approve');

Route::post('/wajah/requests/{id}/reject', [\App\Http\Controllers\Admin\WajahRequestAdminController::class, 'reject'])
    ->name('wajah.requests.reject');
});

// ========================
// KARYAWAN ROUTES (Karyawan, Admin, Super Admin)
// ========================
Route::middleware(['auth', 'role:super_admin|admin|karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [KaryawanDashboard::class, 'index'])->name('dashboard');

    // Pendaftaran Wajah (untuk karyawan yang belum daftar wajah)
    Route::get('/wajah/register', [AbsensiController::class, 'registerFaceForm'])->name('wajah.register');
    Route::post('/wajah/store', [AbsensiController::class, 'registerFaceStore'])->name('wajah.store');

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/masuk', [AbsensiController::class, 'masukForm'])->name('absensi.masuk.form');
    Route::post('/absensi/masuk', [AbsensiController::class, 'masuk'])->name('absensi.masuk');
    Route::get('/absensi/pulang', [AbsensiController::class, 'pulangForm'])->name('absensi.pulang.form');
    Route::post('/absensi/pulang', [AbsensiController::class, 'pulang'])->name('absensi.pulang');
    Route::post('/absensi/verify-face', [AbsensiController::class, 'verifyFace'])->name('absensi.verify');
    Route::get('/absensi/log', [AbsensiController::class, 'log'])->name('absensi.log');

    // Di routes/web.php
    Route::get('/pengajuan', [PengajuanKaryawan::class, 'index'])
        ->name('pengajuan.index');

    Route::get('/pengajuan/create', [PengajuanKaryawan::class, 'create'])
        ->name('pengajuan.create');

    Route::post('/pengajuan/cuti', [PengajuanKaryawan::class, 'storeCuti'])
        ->name('pengajuan.cuti');

    Route::get('/pengajuan/riwayat', [PengajuanKaryawan::class, 'riwayat'])
        ->name('pengajuan.riwayat');

    // [TAMBAHAN] Ada di routes 1, tidak ada di routes 2
    // Diletakkan setelah /riwayat agar route statis tidak tertangkap {id}
    Route::get('/pengajuan/cuti/{id}', [PengajuanKaryawan::class, 'showCuti'])->name('pengajuan.cuti.show');
    Route::post('/pengajuan/cuti/{id}/cancel', [PengajuanKaryawan::class, 'cancelCuti'])->name('pengajuan.cuti.cancel');
    
        //extra off w pengajuan
    Route::get('libur-pengganti/create', [PengajuanKaryawan::class, 'createLiburPengganti'])->name('libur-pengganti.create');
    Route::post('libur-pengganti', [PengajuanKaryawan::class, 'storeLiburPengganti'])->name('libur-pengganti.store');
    Route::post('libur-pengganti/{id}/cancel', [PengajuanKaryawan::class, 'cancelLiburPengganti'])->name('libur-pengganti.cancel');
             Route::get('libur-pengganti/{id}', [PengajuanKaryawan::class, 'showLiburPengganti'])
        ->name('libur-pengganti.show');
        Route::get('libur-pengganti/riwayat', [PengajuanKaryawan::class, 'riwayatLiburPengganti'])
    ->name('libur-pengganti.riwayat');
    
    //pengajuan extra off
    // Route::get('libur-pengganti', [LiburPenggantiController::class, 'index'])->name('libur-pengganti.index');
    // Route::get('libur-pengganti/create', [LiburPenggantiController::class, 'create'])->name('libur-pengganti.create');
    // Route::post('libur-pengganti', [LiburPenggantiController::class, 'store'])->name('libur-pengganti.store');
    // Route::post('libur-pengganti/{id}/cancel', [LiburPenggantiController::class, 'cancel'])->name('libur-pengganti.cancel');

    // Gaji
// Add this to routes/web.php inside karyawan middleware group

Route::prefix('gaji')->name('gaji.')->group(function () {
    // Display list of gaji
    Route::get('/', [KaryawanGajiController::class, 'index'])
        ->name('index');

    // Display detail slip (HTML page)
    Route::get('/{gaji}/slip', [KaryawanGajiController::class, 'slip'])
        ->name('slip');

    // Preview PDF in browser (stream)
    Route::get('/{gaji}/preview', [KaryawanGajiController::class, 'preview'])
        ->name('preview');

    // Download PDF file
    Route::get('/{gaji}/download', [KaryawanGajiController::class, 'download'])
        ->name('download');

    // Get PDF stats (API)
    Route::get('/{gaji}/stats', [KaryawanGajiController::class, 'getPdfStats'])
        ->name('stats');
});

    // Settings
    // Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    // Route::post('/settings/update-photo', [SettingsController::class, 'updatePhoto'])->name('settings.update-photo');
    // Route::post('/settings/update-email', [SettingsController::class, 'updateEmail'])->name('settings.update-email');
    // Route::post('/settings/update-password', [SettingsController::class, 'updatePassword'])->name('settings.update-password');

Route::prefix('ajukan-shift')->name('ajukan-shift.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('karyawan.pengajuan.index');
    })->name('index');
    
    Route::get('/create', [AjukanShiftController::class, 'create'])->name('create');
    Route::post('/store', [AjukanShiftController::class, 'store'])->name('store');
    Route::get('/riwayat', [AjukanShiftController::class, 'riwayat'])->name('riwayat');
    Route::get('/{id}', [AjukanShiftController::class, 'show'])->name('show');
    Route::post('/{id}/cancel', [AjukanShiftController::class, 'cancel'])->name('cancel');
});

    Route::post('/wajah/request', [\App\Http\Controllers\Karyawan\WajahRequestController::class, 'store'])
    ->name('wajah.request');

Route::get('/wajah/capture-baru', [\App\Http\Controllers\Karyawan\WajahRequestController::class, 'captureForm'])
    ->name('wajah.capture-form');

Route::post('/wajah/capture-baru', [\App\Http\Controllers\Karyawan\WajahRequestController::class, 'captureStore'])
    ->name('wajah.capture-store');

    Route::get('/shift-pattern', function () {
    $karyawan = auth()->user()->karyawan;
    $defaultPattern = App\Models\KaryawanShiftPattern::getDefaultPattern($karyawan->id);

    // Current week
    $now = Carbon\Carbon::now();
    $currentWeek = [
        'minggu_ke' => $now->weekOfYear,
        'tahun' => $now->year,
        'start' => $now->startOfWeek(),
        'end' => $now->endOfWeek(),
    ];
    $currentWeekOverride = App\Models\KaryawanShiftPattern::weekly($karyawan->id, $currentWeek['minggu_ke'], $currentWeek['tahun'])->get()->keyBy('hari');

    // Next 4 weeks
    $nextWeeks = [];
    for ($i = 1; $i <= 4; $i++) {
        $week = $now->copy()->addWeeks($i);
        $nextWeeks[] = [
            'minggu_ke' => $week->weekOfYear,
            'tahun' => $week->year,
            'start' => $week->startOfWeek(),
            'end' => $week->endOfWeek(),
        ];
    }

    return view('karyawan.shift_pattern', [
        'defaultPattern' => $defaultPattern,
        'currentWeek' => $currentWeek,
        'currentWeekOverride' => $currentWeekOverride,
        'nextWeeks' => $nextWeeks,
    ]);
})->name('karyawan.shift-pattern');
});

// ========================
// NOTIFIKASI (AUTHENTICATED - ALL ROLES)
// ========================
Route::middleware(['auth'])->group(function() {

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/update-photo', [SettingsController::class, 'updatePhoto'])->name('settings.update-photo');
    Route::post('/settings/update-email', [SettingsController::class, 'updateEmail'])->name('settings.update-email');
    Route::post('/settings/update-password', [SettingsController::class, 'updatePassword'])->name('settings.update-password');
    Route::post('/settings/update-personal', [SettingsController::class, 'updatePersonalData'])->name('settings.update-personal');

    // Display notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])
        ->name('notifikasi.index');

    // Mark as read
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])
        ->name('notifikasi.read');

    Route::post('/notifikasi/mark-read-bulk', [NotifikasiController::class, 'markReadBulk'])
        ->name('notifikasi.mark-read-bulk');

    Route::post('/notifikasi/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])
        ->name('notifikasi.mark-all-read');

    // Delete operations
    Route::post('/notifikasi/delete-bulk', [NotifikasiController::class, 'deleteBulk'])
        ->name('notifikasi.delete-bulk');

    Route::post('/notifikasi/delete-by-time', [NotifikasiController::class, 'deleteByTime'])
        ->name('notifikasi.delete-by-time');

    Route::post('/notifikasi/delete-read', [NotifikasiController::class, 'deleteRead'])
        ->name('notifikasi.delete-read');

    Route::post('/notifikasi/delete-all', [NotifikasiController::class, 'deleteAll'])
        ->name('notifikasi.delete-all');

    // Get unread count (untuk badge)
    Route::get('/notifikasi/unread-count', [NotifikasiController::class, 'getUnreadCount'])
        ->name('notifikasi.unread-count');
});

// routes/web.php

Route::middleware(['auth'])->group(function() {
    // Hanya admin/superadmin yang bisa akses
    Route::get('/admin/compress-old-photos', function() {
        // Check role (sesuaikan dengan sistem role Anda)
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }

        set_time_limit(300); // 5 menit

        $compressed = 0;
        $failed = 0;
        $saved = 0;

        $absensi = \App\Models\Absensi::whereNotNull('foto_masuk')
            ->orWhereNotNull('foto_pulang')
            ->get();

        foreach ($absensi as $item) {
            // Compress foto masuk
            if ($item->foto_masuk && \Storage::disk('public')->exists($item->foto_masuk)) {
                try {
                    $path = storage_path('app/public/' . $item->foto_masuk);
                    $sizeBefore = filesize($path);

                    $image = \Intervention\Image\Facades\Image::make($path);
                    $image->resize(800, 800, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    });
                    $image->encode('jpg', 60);
                    $image->save($path);

                    $sizeAfter = filesize($path);
                    $saved += ($sizeBefore - $sizeAfter);
                    $compressed++;
                } catch (\Exception $e) {
                    $failed++;
                    \Log::error('Compress failed: ' . $e->getMessage());
                }
            }

            // Compress foto pulang
            if ($item->foto_pulang && \Storage::disk('public')->exists($item->foto_pulang)) {
                try {
                    $path = storage_path('app/public/' . $item->foto_pulang);
                    $sizeBefore = filesize($path);

                    $image = \Intervention\Image\Facades\Image::make($path);
                    $image->resize(800, 800, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    });
                    $image->encode('jpg', 60);
                    $image->save($path);

                    $sizeAfter = filesize($path);
                    $saved += ($sizeBefore - $sizeAfter);
                    $compressed++;
                } catch (\Exception $e) {
                    $failed++;
                    \Log::error('Compress failed: ' . $e->getMessage());
                }
            }
        }

        $savedMB = round($saved / (1024 * 1024), 2);

        return response()->json([
            'success' => true,
            'compressed' => $compressed,
            'failed' => $failed,
            'saved_mb' => $savedMB,
            'message' => "✅ Compressed {$compressed} photos, saved {$savedMB} MB"
        ]);
    })->name('admin.compress-photos');
});

