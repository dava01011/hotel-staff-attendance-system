<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            // Personal Data
            $table->string('no_telepon_tambahan', 20)->nullable()->after('no_telepon');
            $table->string('tempat_lahir', 100)->nullable()->after('no_telepon_tambahan');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable()->after('tanggal_lahir');
            $table->enum('status_pernikahan', ['belum_menikah', 'menikah', 'cerai'])->nullable()->after('jenis_kelamin');
            $table->string('golongan_darah', 5)->nullable()->after('status_pernikahan');
            $table->string('agama', 20)->nullable()->after('golongan_darah');

            // Identity & Address
            $table->string('nik', 16)->nullable()->after('agama');
            $table->text('alamat_ktp')->nullable()->after('nik');
            $table->string('kode_pos', 10)->nullable()->after('alamat_ktp');
            $table->text('alamat_tinggal')->nullable()->after('kode_pos');
            $table->string('no_paspor', 30)->nullable()->after('alamat_tinggal');
            $table->date('masa_berlaku_paspor')->nullable()->after('no_paspor');
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn([
                'no_telepon_tambahan',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'status_pernikahan',
                'golongan_darah',
                'agama',
                'nik',
                'alamat_ktp',
                'kode_pos',
                'alamat_tinggal',
                'no_paspor',
                'masa_berlaku_paspor',
            ]);
        });
    }
};
