<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Cuti;
use App\Models\User;

use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Models\Departemen;
use App\Models\CutiApproval;
use App\Models\LokasiKantor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ================= USERS ================= */
        $superAdmin = User::firstOrCreate(
            ['email' => 'super_admin@gmail.com'],
            [
                'nama' => 'Super Admin',
                'password' => Hash::make('123'),
                'role' => 'super_admin',
                'status' => 'aktif'
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Admin',
                'password' => Hash::make('123'),
                'role' => 'admin',
                'status' => 'aktif'
            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'nama' => 'Manager',
                'password' => Hash::make('123'),
                'role' => 'manager',
                'status' => 'aktif'
            ]
        );

        $gm = User::firstOrCreate(
            ['email' => 'gm@gmail.com'],
            [
                'nama' => 'General Manager',
                'password' => Hash::make('123'),
                'role' => 'gm',
                'status' => 'aktif'
            ]
        );

        $hrd = User::firstOrCreate(
            ['email' => 'hrd@gmail.com'],
            [
                'nama' => 'HRD',
                'password' => Hash::make('123'),
                'role' => 'hrd',
                'status' => 'aktif'
            ]
        );

        $karyawanUser = User::firstOrCreate(
            ['email' => 'karyawan@gmail.com'],
            [
                'nama' => 'Karyawan',
                'password' => Hash::make('123'),
                'role' => 'karyawan',
                'status' => 'aktif'
            ]
        );

        /* ================= JABATAN ================= */
        $jabatan = Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Staff'],
            [
                'tipe_gaji' => 'harian',
                'gaji_harian' => 130000,
            ]
        );

        /* ================= LOKASI KANTOR ================= */
        $lokasi = LokasiKantor::firstOrCreate(
            ['nama_lokasi' => 'Hotel Harris'],
            [
                'latitude' => -6.929500847745588,
                'longitude' => 107.58671367327018,
                'radius' => 100
            ]
        );


        $departemen = Departemen::firstOrCreate(
            ['nama' => 'IT'],
        );
        /* ================= JENIS CUTI ================= */
        $tahunan = JenisCuti::firstOrCreate(['nama' => 'Cuti Tahunan']);
        $sakit = JenisCuti::firstOrCreate(['nama' => 'Cuti Sakit']);
        $melahirkan = JenisCuti::firstOrCreate(['nama' => 'Cuti Melahirkan']);
        $khusus = JenisCuti::firstOrCreate(['nama' => 'Cuti Khusus']);


    }
}
