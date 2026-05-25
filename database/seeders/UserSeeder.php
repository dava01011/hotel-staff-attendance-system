<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create([
            'nama' => 'Super Admin',
            'email' => 'super_admin@gmail.com',
            'password' => Hash::make('123'),
            'role' => 'super_admin',
            'status' => 'aktif'
        ]);

                User::create([
            'nama' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('1234'),
            'role' => 'admin',
            'status' => 'aktif'
        ]);

            User::create([
            'nama' => 'Karyawan',
            'email' => 'karyawan@gmail.com',
            'password' => Hash::make('132'),
            'role' => 'karyawan',
            'status' => 'aktif'

        ]);
    }
}
